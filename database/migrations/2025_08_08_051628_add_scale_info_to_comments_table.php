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
            // Field untuk menyimpan scale/zoom level saat komentar dibuat
            $table->decimal('created_at_scale', 4, 2)->default(1.0)->after('status')
                  ->comment('Scale/zoom level saat komentar dibuat (contoh: 1.25 untuk 125%)');

            // Field untuk menyimpan dimensi halaman PDF saat komentar dibuat
            $table->json('page_dimensions')->nullable()->after('created_at_scale')
                  ->comment('Dimensi halaman PDF saat komentar dibuat {width: number, height: number}');

            // Field untuk backup posisi original sebelum normalisasi (optional)
            $table->json('original_position')->nullable()->after('page_dimensions')
                  ->comment('Posisi original sebelum normalisasi untuk debugging');

            // Index untuk performa query berdasarkan scale
            $table->index(['page_number', 'created_at_scale'], 'idx_comments_page_scale');
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
            // Drop index terlebih dahulu
            $table->dropIndex('idx_comments_page_scale');

            // Drop columns
            $table->dropColumn([
                'created_at_scale',
                'page_dimensions',
                'original_position'
            ]);
        });
    }
};
