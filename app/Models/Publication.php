<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'release_date', 'review_deadline', 'creator_id'];

    /**
     * User yang membuat publikasi ini.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Semua dokumen (PDF) yang terkait dengan publikasi ini.
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
    
    /**
     * Para pemeriksa (reviewer) yang ditugaskan untuk publikasi ini.
     */
    public function reviewers()
    {
        return $this->belongsToMany(User::class, 'publication_user', 'publication_id', 'reviewer_id')
                    ->withTimestamps();
    }
}
