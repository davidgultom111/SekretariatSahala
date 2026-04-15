<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Fix: Remove ENUM constraint that causes data truncation error
     * Solution: Convert tipe_surat from ENUM to VARCHAR
     * 
     * The Laravel controller already validates all input values via the
     * LetterTemplateService::getLetterTypes(), so database-level ENUM
     * constraint is redundant and causes issues when changing letter types.
     */
    public function up(): void
    {
        // Get the database driver
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            // Convert ENUM to VARCHAR to allow any string value
            // Laravel validation in the controller will enforce allowed values
            DB::statement("ALTER TABLE letters MODIFY COLUMN tipe_surat VARCHAR(100)");
        } else if ($driver === 'sqlite') {
            // SQLite doesn't support ENUM anyway, so this is a no-op
            // but kept for consistency
            Schema::table('letters', function (Blueprint $table) {
                $table->string('tipe_surat')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            // Revert to the new ENUM values (for the new system)
            DB::statement("ALTER TABLE letters MODIFY COLUMN tipe_surat ENUM(
                'Surat Tugas Pelayanan',
                'Surat Pengantar',
                'Surat Keterangan Jemaat Aktif',
                'Surat Nilai Sekolah',
                'Surat Pengajuan Baptisan',
                'Surat Pengajuan Penyerahan Anak',
                'Surat Pengajuan Pernikahan'
            )");
        }
    }
};
