<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['document_id', 'user_id', 'page_number', 'type', 'position', 'content', 'status'];
    
    // Memberitahu Laravel bahwa kolom 'position' harus di-handle sebagai JSON
    protected $casts = [
        'position' => 'array',
    ];

    /**
     * Dokumen tempat komentar ini berada.
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * User yang membuat komentar ini.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi untuk mendapatkan komentar induk (jika ada).
     */
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Relasi untuk mendapatkan semua balasan dari sebuah komentar.
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
