<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Document;
use App\Models\Comment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ambil user pemeriksa dan dokumen pertama yang ada di database
        $reviewer = User::where('email', 'pemeriksa@bps.go.id')->first();
        $document = Document::first();

        // Pastikan user dan dokumen ditemukan sebelum melanjutkan
        if ($reviewer && $document) {
            // Gunakan factory untuk membuat 50 komentar
            Comment::factory()
                ->count(50)
                ->create([
                    'user_id' => $reviewer->id,
                    'document_id' => $document->id,
                ]);

            $this->command->info('50 komentar acak berhasil ditambahkan.');
        } else {
            $this->command->error('User pemeriksa atau dokumen tidak ditemukan. Seeding komentar dibatalkan.');
        }
    }
}
