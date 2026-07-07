<?php

namespace App\Console\Commands;

use App\Jobs\SendReviewerAssignmentNotificationJob;
use App\Models\ReviewerAssignmentNotification;
use Illuminate\Console\Command;

class SendReviewerAssignmentNotifications extends Command
{
    protected $signature = 'app:send-reviewer-assignment-notifications';
    protected $description = 'Send queued WhatsApp notifications for newly assigned reviewers';

    public function handle(): int
    {
        $notifications = ReviewerAssignmentNotification::where('status', 'pending')
            ->with(['publication', 'reviewer', 'assignor'])
            ->orderBy('created_at')
            ->get();

        foreach ($notifications as $notification) {
            if (!$notification->reviewer || empty($notification->reviewer->no_hp)) {
                $notification->update([
                    'status' => 'failed',
                    'last_error' => 'Reviewer tidak memiliki nomor WhatsApp',
                ]);
                continue;
            }

            $this->info('Mengirim notifikasi WA ke ' . $notification->reviewer->fullname . ' untuk publikasi ' . $notification->publication->name);

            $job = new SendReviewerAssignmentNotificationJob([
                'notification_id' => $notification->id,
                'no_hp' => $notification->reviewer->no_hp,
                'message' => $notification->message,
            ]);

            $job->handle();
            $notification->refresh();
        }

        return Command::SUCCESS;
    }
}
