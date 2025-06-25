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
        Schema::create('publication_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('publication_id')->constrained()->onDelete('cascade');

            // Ini adalah pemeriksa yang ditugaskan
            $table->foreignId('reviewer_id')->constrained('users');

            // Opsional: siapa yang menugaskan
            $table->foreignId('assignor_id')->nullable()->constrained('users');

            $table->timestamps();

            // Memastikan kombinasi publikasi dan reviewer unik
            $table->unique(['publication_id', 'reviewer_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('publication_user');
    }
};
