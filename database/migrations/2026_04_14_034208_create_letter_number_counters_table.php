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
        Schema::create('letter_number_counters', function (Blueprint $table) {
            $table->id();
            $table->string('letter_type')->unique(); // surat_tugas_pelayanan, dll
            $table->integer('year'); // 2026, 2027, dll
            $table->integer('next_number')->default(1); // Nomor urut berikutnya
            $table->string('abbreviation'); // TP, SP, KJA, NS, PB, PA, PP
            $table->timestamps();
            
            // Index untuk performa query
            $table->index(['letter_type', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_number_counters');
    }
};
