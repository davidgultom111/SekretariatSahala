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
            // Kolom khusus untuk Surat Pengajuan Pernikahan
            $table->foreignId('member_pria_id')->nullable()->constrained('members')->onDelete('restrict');
            $table->foreignId('member_wanita_id')->nullable()->constrained('members')->onDelete('restrict');
            $table->date('tanggal_pernikahan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->dropForeignIfExists(['member_pria_id']);
            $table->dropForeignIfExists(['member_wanita_id']);
            $table->dropColumn(['member_pria_id', 'member_wanita_id', 'tanggal_pernikahan']);
        });
    }
};
