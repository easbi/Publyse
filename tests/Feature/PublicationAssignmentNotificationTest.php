<?php

use App\Models\Publication;
use App\Models\ReviewerAssignmentNotification;
use App\Models\User;

test('it creates a pending notification when a new reviewer is assigned', function () {
    $assignor = User::factory()->create();
    $reviewer = User::factory()->create([
        'name' => 'Reviewer Test',
    ]);

    $reviewer->no_hp = '6281234567890';
    $reviewer->save();

    $publication = Publication::create([
        'name' => 'Publikasi Uji Coba',
        'release_date' => now()->addDays(10)->toDateString(),
        'review_deadline' => now()->addDays(20)->toDateString(),
        'creator_id' => $assignor->id,
    ]);

    $this->actingAs($assignor)
        ->post(route('publications.assign.sync', $publication), [
            'reviewers' => [$reviewer->id],
        ])
        ->assertRedirect(route('publications.index'));

    $this->assertDatabaseHas('reviewer_assignment_notifications', [
        'publication_id' => $publication->id,
        'reviewer_id' => $reviewer->id,
        'assignor_id' => $assignor->id,
        'status' => 'pending',
    ]);

    $notification = ReviewerAssignmentNotification::where('publication_id', $publication->id)
        ->where('reviewer_id', $reviewer->id)
        ->first();

    expect($notification->message)->toContain($publication->name);
});
