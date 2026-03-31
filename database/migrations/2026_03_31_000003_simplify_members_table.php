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
        Schema::table('members', function (Blueprint $table) {
            // Drop unnecessary columns
            $table->dropColumn([
                'no_identitas',
                'kelurahan',
                'kecamatan',
                'kota',
                'provinsi',
                'kode_pos',
                'email',
                'status_perkawinan',
                'pekerjaan',
                'tanggal_bergabung',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('no_identitas')->unique()->after('tempat_lahir');
            $table->string('kelurahan')->after('alamat');
            $table->string('kecamatan')->after('kelurahan');
            $table->string('kota')->after('kecamatan');
            $table->string('provinsi')->after('kota');
            $table->string('kode_pos')->after('provinsi');
            $table->string('email')->nullable()->after('no_telepon');
            $table->string('status_perkawinan')->after('email');
            $table->string('pekerjaan')->nullable()->after('status_perkawinan');
            $table->date('tanggal_bergabung')->after('pekerjaan');
        });
    }
};
