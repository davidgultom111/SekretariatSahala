<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Letter extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'tipe_surat',
        'nomor_surat',
        'tanggal_surat',
        'keterangan',
        'isi_surat',
        'file_path',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
    ];

    /**
     * Get the member that owns the letter.
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
