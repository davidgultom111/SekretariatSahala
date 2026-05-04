<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\BaseController;
use App\Http\Resources\LetterResource;
use App\Models\Letter;
use App\Models\PengajuanSurat;
use App\Services\LetterTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// PengajuanController menangani review, persetujuan, dan penolakan pengajuan surat dari jemaat oleh admin
class PengajuanController extends BaseController
{
    // API menangani daftar semua pengajuan surat dengan filter status (Dalam Proses / Disetujui / Ditolak)
    public function index(Request $request): JsonResponse
    {
        $pengajuans = PengajuanSurat::with(['member'])
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->orderByDesc('created_at')
            ->get();

        return $this->success($pengajuans);
    }

    // API menangani tampil detail pengajuan beserta data member, mempelai, dan surat terkait
    public function show(PengajuanSurat $pengajuan): JsonResponse
    {
        return $this->success(
            $pengajuan->load(['member', 'memberPria', 'memberWanita', 'letter'])
        );
    }

    // API menangani persetujuan pengajuan surat — generate nomor surat dan buat record Letter baru
    public function approve(Request $request, PengajuanSurat $pengajuan): JsonResponse
    {
        if ($pengajuan->status !== 'Dalam Proses') {
            return $this->error('Pengajuan ini sudah diproses sebelumnya.', 422);
        }

        $request->validate(['tanggal_surat' => 'nullable|date']);

        $allTypes     = LetterTemplateService::getLetterTypes();
        $nomorSurat   = LetterTemplateService::generateLetterNumber($pengajuan->letter_type);
        $tanggalSurat = $request->input('tanggal_surat', today()->toDateString());

        // Surat pernikahan menggunakan member_pria_id sebagai member_id utama surat
        $isPernikahan = $pengajuan->letter_type === 'surat_pengajuan_pernikahan';
        $memberId     = $isPernikahan ? $pengajuan->member_pria_id : $pengajuan->member_id;

        // Data dasar surat yang berlaku untuk semua tipe
        $letterData = [
            'member_id'     => $memberId,
            'tipe_surat'    => $allTypes[$pengajuan->letter_type],
            'letter_type'   => $pengajuan->letter_type,
            'nomor_surat'   => $nomorSurat,
            'tanggal_surat' => $tanggalSurat,
            'keterangan'    => $pengajuan->keterangan,
        ];

        // Field tambahan yang berbeda per tipe surat, disalin dari data pengajuan
        $typeFields = [
            'surat_tugas_pelayanan'           => ['tgl_mulai_tugas', 'tgl_akhir_tugas', 'tujuan_tugas'],
            'surat_keterangan_jemaat_aktif'   => ['tahun_bergabung'],
            'surat_nilai_sekolah'             => ['asal_sekolah', 'kelas', 'semester', 'nilai'],
            'surat_pengajuan_penyerahan_anak' => ['nama_ayah', 'nama_ibu', 'nama_anak', 'tempat_lahir_anak', 'tanggal_lahir_anak'],
            'surat_pengajuan_pernikahan'      => ['member_pria_id', 'member_wanita_id', 'tanggal_pernikahan'],
        ];

        foreach ($typeFields[$pengajuan->letter_type] ?? [] as $field) {
            if (!is_null($pengajuan->{$field})) {
                $letterData[$field] = $pengajuan->{$field};
            }
        }

        $letter = Letter::create($letterData);

        // Tandai pengajuan sebagai disetujui dan hubungkan ke surat yang baru dibuat
        $pengajuan->update(['status' => 'Disetujui', 'letter_id' => $letter->id]);

        return $this->success(
            ['letter_id' => $letter->id, 'nomor_surat' => $letter->nomor_surat],
            'Pengajuan disetujui dan surat berhasil dibuat.'
        );
    }

    // API menangani penolakan pengajuan surat dengan catatan alasan dari admin
    public function reject(Request $request, PengajuanSurat $pengajuan): JsonResponse
    {
        if ($pengajuan->status !== 'Dalam Proses') {
            return $this->error('Pengajuan ini sudah diproses sebelumnya.', 422);
        }

        $pengajuan->update([
            'status'  => 'Ditolak',
            'catatan' => $request->input('catatan'),
        ]);

        return $this->success(null, 'Pengajuan berhasil ditolak.');
    }

    // API menangani hapus data pengajuan dari database
    public function destroy(PengajuanSurat $pengajuan): JsonResponse
    {
        $pengajuan->delete();
        return $this->noContent();
    }
}
