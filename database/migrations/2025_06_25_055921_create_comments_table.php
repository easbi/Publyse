<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke dokumen spesifik, bukan ke publikasi
            $table->foreignId('document_id')->constrained()->onDelete('cascade');

            // Foreign Key ke user yang memberi komentar (pemeriksa)
            $table->foreignId('user_id')->constrained('users');

            $table->integer('page_number'); // Menggantikan `halaman`
            $table->enum('type', ['point', 'area']); // Jenis anotasi
            $table->json('position')->nullable(); // Menyimpan koordinat x, y, dll.
            $table->text('content'); // Menggantikan `comment`

            // Menggantikan `is_tindak_lanjut` dengan sistem status
            $table->enum('status', ['open', 'done'])->default('open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
};
