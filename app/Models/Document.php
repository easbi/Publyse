<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ['publication_id', 'original_filename', 'stored_path', 'version', 'uploader_id'];
    protected $appends = ['pdf_url'];

    /**
     * Publikasi induk dari dokumen ini.
     */
    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }

    /**
     * User yang mengunggah dokumen ini.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }

    /**
     * Semua komentar yang ada pada dokumen ini.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getPdfUrlAttribute()
    {
        return asset('storage/' . $this->stored_path);
    }

}
