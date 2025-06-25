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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke publikasi induknya
            $table->foreignId('publication_id')->constrained()->onDelete('cascade');

            $table->string('original_filename');
            $table->string('stored_path'); // Path file di server
            $table->integer('version')->default(1);

            // Foreign Key ke user yang mengunggah file
            $table->foreignId('uploader_id')->constrained('users');
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
        Schema::dropIfExists('documents');
    }
};
