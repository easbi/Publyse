<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publikasi extends Model
{
    use HasFactory;
    protected $table = 'master_publikasi';
    protected $fillable=[
        'nama_publikasi',
        'batas_upload',
        'waktu_rilis',
        'batas_pemeriksaan',
        'batas_tl',
        'created_by'
    ];
}
