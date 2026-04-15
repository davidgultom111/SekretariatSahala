<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            // Kolom khusus untuk Surat Nilai Sekolah
            $table->string('asal_sekolah')->nullable();
            $table->string('kelas')->nullable();
            $table->string('semester')->nullable();
            $table->integer('nilai')->default(90)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->dropColumn(['asal_sekolah', 'kelas', 'semester', 'nilai']);
        });
    }
};
