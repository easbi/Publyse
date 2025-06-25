<?php

// database/seeders/MainSeeder.php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Publication;
use App\Models\Document;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Buat User Pembuat & Pemeriksa
        $pembuat = User::create([
            'name' => 'Admin Pembuat',
            'nip' => '111111',
            'email' => 'pembuat@bps.go.id',
            'password' => Hash::make('password'),
        ]);

        $pemeriksa = User::create([
            'name' => 'Susi Pemeriksa',
            'nip' => '222222',
            'email' => 'pemeriksa@bps.go.id',
            'password' => Hash::make('password'),
        ]);

        // 2. Buat Publikasi baru oleh 'pembuat'
        $publikasi = Publication::create([
            'name' => 'Publikasi Statistik Transportasi Triwulanan',
            'release_date' => now()->addMonths(3),
            'review_deadline' => now()->addMonth(),
            'creator_id' => $pembuat->id,
        ]);

        // 3. Tambahkan dokumen PDF ke publikasi
        Document::create([
            'publication_id' => $publikasi->id,
            'original_filename' => 'statistik-transportasi-2025.pdf',
            'stored_path' => 'documents/contoh-publikasi.pdf', // Path relatif dari storage/app/public
            'version' => 1,
            'uploader_id' => $pembuat->id,
        ]);

        // 4. Tugaskan 'pemeriksa' untuk mereview publikasi ini
        $publikasi->reviewers()->attach($pemeriksa->id, ['assignor_id' => $pembuat->id]);

        $this->command->info('Database seeding selesai!');
    }
}