<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// LetterResource menangani format output data surat pada semua response API
class LetterResource extends JsonResource
{
    // API menangani transformasi model Letter menjadi array JSON yang dikirim ke client
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'member_id' => $this->member_id,

            // Data jemaat utama (dimuat hanya jika relasi di-load oleh controller)
            'member' => $this->whenLoaded('member', fn () => [
                'id'            => $this->member->id,
                'id_jemaat'     => $this->member->id_jemaat,
                'nama_lengkap'  => $this->member->nama_lengkap,
                'tempat_lahir'  => $this->member->tempat_lahir,
                'tanggal_lahir' => $this->member->tanggal_lahir?->format('Y-m-d'),
                'alamat'        => $this->member->alamat,
                'no_telepon'    => $this->member->no_telepon,
                'jenis_kelamin' => $this->member->jenis_kelamin,
            ]),

            // Data mempelai pria (hanya untuk surat_pengajuan_pernikahan)
            'member_pria' => $this->whenLoaded('memberPria', fn () => [
                'id'            => $this->memberPria->id,
                'id_jemaat'     => $this->memberPria->id_jemaat,
                'nama_lengkap'  => $this->memberPria->nama_lengkap,
                'tempat_lahir'  => $this->memberPria->tempat_lahir,
                'tanggal_lahir' => $this->memberPria->tanggal_lahir?->format('Y-m-d'),
                'alamat'        => $this->memberPria->alamat,
                'no_telepon'    => $this->memberPria->no_telepon,
            ]),

            // Data mempelai wanita (hanya untuk surat_pengajuan_pernikahan)
            'member_wanita' => $this->whenLoaded('memberWanita', fn () => [
                'id'            => $this->memberWanita->id,
                'id_jemaat'     => $this->memberWanita->id_jemaat,
                'nama_lengkap'  => $this->memberWanita->nama_lengkap,
                'tempat_lahir'  => $this->memberWanita->tempat_lahir,
                'tanggal_lahir' => $this->memberWanita->tanggal_lahir?->format('Y-m-d'),
                'alamat'        => $this->memberWanita->alamat,
                'no_telepon'    => $this->memberWanita->no_telepon,
            ]),

            // Identitas surat
            'tipe_surat'    => $this->tipe_surat,    // nama tampilan, misal "Surat Pengantar"
            'letter_type'   => $this->letter_type,   // slug, misal "surat_pengantar"
            'nomor_surat'   => $this->nomor_surat,
            'tanggal_surat' => $this->tanggal_surat?->format('Y-m-d'),
            'keterangan'    => $this->keterangan,

            // Field khusus surat_tugas_pelayanan
            'tgl_mulai_tugas' => $this->tgl_mulai_tugas,
            'tgl_akhir_tugas' => $this->tgl_akhir_tugas,
            'tujuan_tugas'    => $this->tujuan_tugas,

            // Field khusus surat_keterangan_jemaat_aktif
            'tahun_bergabung' => $this->tahun_bergabung,

            // Field khusus surat_nilai_sekolah
            'asal_sekolah' => $this->asal_sekolah,
            'kelas'        => $this->kelas,
            'semester'     => $this->semester,
            'nilai'        => $this->nilai,

            // Field khusus surat_pengajuan_penyerahan_anak
            'nama_ayah'          => $this->nama_ayah,
            'nama_ibu'           => $this->nama_ibu,
            'nama_anak'          => $this->nama_anak,
            'tempat_lahir_anak'  => $this->tempat_lahir_anak,
            'tanggal_lahir_anak' => $this->tanggal_lahir_anak,

            // Field khusus surat_pengajuan_pernikahan
            'member_pria_id'     => $this->member_pria_id,
            'member_wanita_id'   => $this->member_wanita_id,
            'tanggal_pernikahan' => $this->tanggal_pernikahan,

            // Informasi file PDF (true jika sudah pernah di-generate dan tersimpan)
            'has_pdf' => (bool) $this->pdf_path,
            'pdf_url' => $this->pdf_path
                ? url("api/admin/letters/{$this->id}/pdf")
                : null,

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
