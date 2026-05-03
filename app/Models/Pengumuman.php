<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    protected $table = 'pengumuman';

    protected $fillable = [
        'judul',
        'isi',
        'tanggal_mulai',
        'tanggal_akhir',
        'aktif',
    ];

    protected $casts = [
        'aktif'          => 'boolean',
        'tanggal_mulai'  => 'date',
        'tanggal_akhir'  => 'date',
    ];
}
