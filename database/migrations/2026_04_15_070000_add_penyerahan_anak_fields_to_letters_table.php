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
            // Kolom khusus untuk Surat Penyerahan Anak
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('nama_anak')->nullable();
            $table->string('tempat_lahir_anak')->nullable();
            $table->date('tanggal_lahir_anak')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->dropColumn(['nama_ayah', 'nama_ibu', 'nama_anak', 'tempat_lahir_anak', 'tanggal_lahir_anak']);
        });
    }
};
