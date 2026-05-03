<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalPelayanan extends Model
{
    protected $table = 'jadwal_pelayanan';

    protected $fillable = [
        'nama_kegiatan',
        'kategori',
        'deskripsi',
        'hari',
        'waktu',
        'urutan',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'urutan' => 'integer',
    ];
}
