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
            $table->string('id_jemaat')->unique()->nullable()->after('id');
            $table->string('password')->nullable()->after('status_aktif');
            $table->string('role')->default('member')->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('id_jemaat');
            $table->dropColumn('password');
            $table->dropColumn('role');
        });
    }
};
