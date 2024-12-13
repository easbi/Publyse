<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemeriksaan extends Model
{
    use HasFactory;
    protected $table = 'transaksi_pemeriksaan';
    protected $fillable=[
        'publikasi_id',
        'pemeriksa_nip',
        'bagian_pemeriksaan',
        'halaman',
        'hasil_pemeriksaan',
        'is_tindak_lanjut',
        'user_nip_pl',
    ];
}
