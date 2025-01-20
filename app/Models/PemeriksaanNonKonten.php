<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemeriksaanNonKonten extends Model
{
    use HasFactory;
    protected $table = 'transaksi_pemeriksaannk';
    protected $fillable=[
        'publikasi_id',
        'pemeriksa_nip',
        'bagian_pemeriksaan',
        'hasil_pemeriksaan',
        'is_tindak_lanjut',
        'user_nip_pl',
    ];
}
