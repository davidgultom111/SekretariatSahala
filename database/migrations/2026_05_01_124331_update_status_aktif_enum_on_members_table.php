<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE members SET status_aktif = 'Tidak Aktif' WHERE status_aktif = 'Dipindahkan'");
        DB::statement("ALTER TABLE members MODIFY COLUMN status_aktif ENUM('Aktif', 'Tidak Aktif') NOT NULL DEFAULT 'Aktif'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE members MODIFY COLUMN status_aktif ENUM('Aktif', 'Tidak Aktif', 'Dipindahkan') NOT NULL DEFAULT 'Aktif'");
    }
};
