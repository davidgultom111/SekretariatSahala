<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GaleriFoto extends Model
{
    protected $table = 'galeri_foto';

    protected $fillable = [
        'judul',
        'deskripsi',
        'foto',
        'urutan',
    ];

    protected $appends = ['foto_url'];

    protected $casts = [
        'urutan' => 'integer',
    ];

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? Storage::disk('public')->url($this->foto) : null;
    }
}
