<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pengajuan_surat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members');
            $table->string('letter_type', 50);
            $table->string('tipe_surat', 100);
            $table->string('status', 20)->default('Dalam Proses'); // Dalam Proses | Disetujui | Ditolak
            $table->foreignId('letter_id')->nullable()->constrained('letters')->nullOnDelete();
            $table->text('catatan')->nullable();

            // Bidang umum
            $table->text('keterangan')->nullable();

            // surat_tugas_pelayanan
            $table->date('tgl_mulai_tugas')->nullable();
            $table->date('tgl_akhir_tugas')->nullable();
            $table->text('tujuan_tugas')->nullable();

            // surat_keterangan_jemaat_aktif
            $table->unsignedSmallInteger('tahun_bergabung')->nullable();

            // surat_nilai_sekolah
            $table->string('asal_sekolah', 100)->nullable();
            $table->string('kelas', 20)->nullable();
            $table->string('semester', 10)->nullable();
            $table->decimal('nilai', 5, 2)->nullable();

            // surat_pengajuan_penyerahan_anak
            $table->string('nama_ayah', 100)->nullable();
            $table->string('nama_ibu', 100)->nullable();
            $table->string('nama_anak', 100)->nullable();
            $table->string('tempat_lahir_anak', 100)->nullable();
            $table->date('tanggal_lahir_anak')->nullable();

            // surat_pengajuan_pernikahan
            $table->unsignedBigInteger('member_pria_id')->nullable();
            $table->unsignedBigInteger('member_wanita_id')->nullable();
            $table->date('tanggal_pernikahan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_surat');
    }
};
