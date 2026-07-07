<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ReviewerAssignmentNotification;
use Illuminate\Support\Facades\Log;

class SendReviewerAssignmentNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $details;
    public $timeout = 20;
    public $tries = 3;
    public $backoff = [10, 30, 60];

    public function __construct(array $details)
    {
        $this->details = $details;
    }

    public function handle(): void
    {
        $notification = null;
        if (!empty($this->details['notification_id'])) {
            $notification = ReviewerAssignmentNotification::find($this->details['notification_id']);
        }

        Log::info('Starting reviewer assignment WA notification', [
            'notification_id' => $notification?->id,
            'reviewer_id' => $notification?->reviewer_id,
            'publication_id' => $notification?->publication_id,
            'number' => $this->details['no_hp'] ?? null,
        ]);

        $token = env('API_WA_TOKEN');
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://app.ruangwa.id/api/send_message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query([
                'token' => $token,
                'number' => $this->details['no_hp'],
                'message' => $this->details['message'],
            ]),
        ]);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $errorMessage = 'Curl error: ' . curl_error($curl);
            Log::error($errorMessage, ['notification_id' => $notification?->id]);
            if ($notification) {
                $notification->update([
                    'status' => 'failed',
                    'last_error' => $errorMessage,
                ]);
            }
        } else {
            Log::info('Response from WA API', [
                'notification_id' => $notification?->id,
                'response' => $response,
            ]);
            if ($notification) {
                $notification->update([
                    'status' => 'sent',
                    'last_error' => null,
                    'sent_at' => now(),
                ]);
            }
        }

        curl_close($curl);
    }
}
