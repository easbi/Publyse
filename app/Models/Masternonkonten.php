<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Masternonkonten extends Model
{
    use HasFactory;
    protected $table = 'master_non_konten';
    protected $fillable=[
        'bagian_publikasi',
        'rincian',
        'created_by',
    ];
}
