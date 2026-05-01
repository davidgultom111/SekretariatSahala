<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// LetterNumberCounter menangani penomoran surat otomatis per tipe dan per tahun
class LetterNumberCounter extends Model
{
    // Field yang boleh diisi secara massal
    protected $fillable = [
        'letter_type',   // slug tipe surat, misal "surat_pengantar"
        'year',          // tahun penomoran, reset setiap tahun baru
        'next_number',   // nomor urut berikutnya yang akan dipakai
        'abbreviation',  // singkatan tipe surat, misal "SP", "TP", "KJA"
    ];

    // Cast ke tipe integer agar kalkulasi nomor surat tidak error
    protected $casts = [
        'year'        => 'integer',
        'next_number' => 'integer',
    ];
}
