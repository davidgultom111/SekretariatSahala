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
            $table->date('tgl_mulai_tugas')->nullable()->after('tanggal_surat');
            $table->date('tgl_akhir_tugas')->nullable()->after('tgl_mulai_tugas');
            $table->text('tujuan_tugas')->nullable()->after('tgl_akhir_tugas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->dropColumn(['tgl_mulai_tugas', 'tgl_akhir_tugas', 'tujuan_tugas']);
        });
    }
};
