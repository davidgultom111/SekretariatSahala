<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Model Letter merepresentasikan surat gereja yang dibuat oleh admin
class Letter extends Model
{
    use HasFactory;

    // Field yang boleh diisi secara massal (mass assignment)
    protected $fillable = [
        'member_id',       // jemaat utama pemilik surat
        'tipe_surat',      // nama tampilan, misal "Surat Pengantar"
        'letter_type',     // slug, misal "surat_pengantar"
        'nomor_surat',     // format: NNN/GPdI/SA/ABBREV/YEAR
        'tanggal_surat',
        'tahun_bergabung', // khusus surat_keterangan_jemaat_aktif
        'tgl_mulai_tugas', // khusus surat_tugas_pelayanan
        'tgl_akhir_tugas', // khusus surat_tugas_pelayanan
        'tujuan_tugas',    // khusus surat_tugas_pelayanan
        'keterangan',      // khusus surat_pengantar dan umum
        'isi_surat',
        'template_content',
        'file_path',
        'pdf_path',        // path file PDF yang tersimpan di storage/app/letters/
        'asal_sekolah',    // khusus surat_nilai_sekolah
        'kelas',           // khusus surat_nilai_sekolah
        'semester',        // khusus surat_nilai_sekolah
        'nilai',           // khusus surat_nilai_sekolah
        'nama_ayah',       // khusus surat_pengajuan_penyerahan_anak
        'nama_ibu',        // khusus surat_pengajuan_penyerahan_anak
        'nama_anak',       // khusus surat_pengajuan_penyerahan_anak
        'tempat_lahir_anak',   // khusus surat_pengajuan_penyerahan_anak
        'tanggal_lahir_anak',  // khusus surat_pengajuan_penyerahan_anak
        'member_pria_id',      // khusus surat_pengajuan_pernikahan
        'member_wanita_id',    // khusus surat_pengajuan_pernikahan
        'tanggal_pernikahan',  // khusus surat_pengajuan_pernikahan
    ];

    // Cast kolom tanggal surat ke tipe Carbon date
    protected $casts = [
        'tanggal_surat' => 'date',
    ];

    // Relasi: surat dimiliki oleh satu jemaat utama
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    // Relasi: mempelai pria pada surat pernikahan
    public function memberPria()
    {
        return $this->belongsTo(Member::class, 'member_pria_id');
    }

    // Relasi: mempelai wanita pada surat pernikahan
    public function memberWanita()
    {
        return $this->belongsTo(Member::class, 'member_wanita_id');
    }
}
