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
            // Drop existing foreign keys with RESTRICT
            $table->dropForeign(['member_pria_id']);
            $table->dropForeign(['member_wanita_id']);
            
            // Recreate with CASCADE
            $table->foreign('member_pria_id')
                ->references('id')
                ->on('members')
                ->onDelete('cascade');
                
            $table->foreign('member_wanita_id')
                ->references('id')
                ->on('members')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            // First drop CASCADE constraints
            $table->dropForeign(['member_pria_id']);
            $table->dropForeign(['member_wanita_id']);
            
            // Recreate with RESTRICT
            $table->foreign('member_pria_id')
                ->references('id')
                ->on('members')
                ->onDelete('restrict');
                
            $table->foreign('member_wanita_id')
                ->references('id')
                ->on('members')
                ->onDelete('restrict');
        });
    }
};
