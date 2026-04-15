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
        'letter_type',
        'nomor_surat',
        'tanggal_surat',
        'tahun_bergabung',
        'tgl_mulai_tugas',
        'tgl_akhir_tugas',
        'tujuan_tugas',
        'keterangan',
        'isi_surat',
        'template_content',
        'file_path',
        'pdf_path',
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
