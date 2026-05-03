<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('jadwal_pelayanan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kegiatan', 100);
            $table->string('kategori', 50);
            $table->text('deskripsi')->nullable();
            $table->string('hari', 20);
            $table->string('waktu', 10);
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_pelayanan');
    }
};
