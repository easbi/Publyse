<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Publikasi yang dibuat oleh user ini.
     */
    public function createdPublications()
    {
        return $this->hasMany(Publication::class, 'creator_id');
    }

    /**
     * Publikasi yang ditugaskan untuk direview oleh user ini.
     */
    public function assignedPublications()
    {
        return $this->belongsToMany(Publication::class, 'publication_user', 'reviewer_id', 'publication_id')
                    ->withTimestamps();
    }

    /**
     * Komentar yang dibuat oleh user ini.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    
    /**
     * Dokumen yang diunggah oleh user ini.
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'uploader_id');
    }
}
