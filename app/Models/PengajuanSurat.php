<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanSurat extends Model
{
    protected $table = 'pengajuan_surat';

    protected $fillable = [
        'member_id', 'letter_type', 'tipe_surat', 'status', 'letter_id', 'catatan',
        'keterangan',
        'tgl_mulai_tugas', 'tgl_akhir_tugas', 'tujuan_tugas',
        'tahun_bergabung',
        'asal_sekolah', 'kelas', 'semester', 'nilai',
        'nama_ayah', 'nama_ibu', 'nama_anak', 'tempat_lahir_anak', 'tanggal_lahir_anak',
        'member_pria_id', 'member_wanita_id', 'tanggal_pernikahan',
    ];

    protected $casts = [
        'tgl_mulai_tugas'    => 'date',
        'tgl_akhir_tugas'    => 'date',
        'tanggal_lahir_anak' => 'date',
        'tanggal_pernikahan' => 'date',
        'nilai'              => 'float',
        'tahun_bergabung'    => 'integer',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function memberPria(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_pria_id');
    }

    public function memberWanita(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_wanita_id');
    }

    public function letter(): BelongsTo
    {
        return $this->belongsTo(Letter::class);
    }
}
