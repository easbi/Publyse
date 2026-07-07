<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewerAssignmentNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'publication_id',
        'reviewer_id',
        'assignor_id',
        'status',
        'message',
        'last_error',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function assignor()
    {
        return $this->belongsTo(User::class, 'assignor_id');
    }
}
