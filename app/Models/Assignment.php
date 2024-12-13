<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;
    protected $table = 'assign_pemeriksa';
    protected $fillable=[
        'publikasi_id',
        'pemeriksa_nip',
        'created_by',
    ];
}
