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
        Schema::create('letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->enum('tipe_surat', [
                'Surat Baptisan',
                'Surat Pernikahan',
                'Surat Serah Nikah',
                'Surat Kematian',
                'Surat Keluar Jemaat',
                'Surat Masuk Jemaat',
                'Surat Keterangan Jemaat',
                'Surat Rekomendasi'
            ]);
            $table->string('nomor_surat')->unique();
            $table->date('tanggal_surat');
            $table->text('keterangan')->nullable();
            $table->text('isi_surat');
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letters');
    }
};
