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
        Schema::table('comments', function (Blueprint $table) {
            // Kolom ini akan menyimpan ID dari komentar induk.
            // Bisa bernilai NULL jika ini adalah komentar utama (bukan balasan).
            $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade')->after('document_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            if (Schema::hasColumn('comments', 'parent_id')) {
                $table->dropColumn('parent_id');
            }
        });
    }
};
