<?php

use App\Models\Document;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('menolak unggahan duplikat saat token upload yang sama dikirim ulang', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $publication = Publication::create([
        'name' => 'Publikasi Uji',
        'release_date' => now()->toDateString(),
        'review_deadline' => now()->addDay()->toDateString(),
        'creator_id' => $user->id,
    ]);

    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
    $token = 'upload-token-123';

    $firstResponse = $this->actingAs($user)
        ->withSession([
            'document-upload-token:' . $publication->id => $token,
        ])
        ->post(route('documents.store', $publication), [
            'document_file' => $file,
            'upload_token' => $token,
        ]);

    $firstResponse->assertRedirect();
    expect(Document::where('publication_id', $publication->id)->count())->toBe(1);

    $secondResponse = $this->actingAs($user)
        ->withSession([
            'document-upload-token:' . $publication->id => $token,
            'document-upload-used:' . $publication->id => [$token],
        ])
        ->post(route('documents.store', $publication), [
            'document_file' => $file,
            'upload_token' => $token,
        ]);

    $secondResponse->assertRedirect();
    $secondResponse->assertSessionHas('error');
    expect(Document::where('publication_id', $publication->id)->count())->toBe(1);
});
