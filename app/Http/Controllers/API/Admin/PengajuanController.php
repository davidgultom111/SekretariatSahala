<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\BaseController;
use App\Http\Resources\LetterResource;
use App\Models\Letter;
use App\Models\PengajuanSurat;
use App\Services\LetterTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PengajuanController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $pengajuans = PengajuanSurat::with(['member'])
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->orderByDesc('created_at')
            ->get();

        return $this->success($pengajuans);
    }

    public function show(PengajuanSurat $pengajuan): JsonResponse
    {
        return $this->success(
            $pengajuan->load(['member', 'memberPria', 'memberWanita', 'letter'])
        );
    }

    public function approve(Request $request, PengajuanSurat $pengajuan): JsonResponse
    {
        if ($pengajuan->status !== 'Dalam Proses') {
            return $this->error('Pengajuan ini sudah diproses sebelumnya.', 422);
        }

        $request->validate(['tanggal_surat' => 'nullable|date']);

        $allTypes     = LetterTemplateService::getLetterTypes();
        $nomorSurat   = LetterTemplateService::generateLetterNumber($pengajuan->letter_type);
        $tanggalSurat = $request->input('tanggal_surat', today()->toDateString());

        $isPernikahan = $pengajuan->letter_type === 'surat_pengajuan_pernikahan';
        $memberId     = $isPernikahan ? $pengajuan->member_pria_id : $pengajuan->member_id;

        $letterData = [
            'member_id'     => $memberId,
            'tipe_surat'    => $allTypes[$pengajuan->letter_type],
            'letter_type'   => $pengajuan->letter_type,
            'nomor_surat'   => $nomorSurat,
            'tanggal_surat' => $tanggalSurat,
            'keterangan'    => $pengajuan->keterangan,
        ];

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

        $pengajuan->update(['status' => 'Disetujui', 'letter_id' => $letter->id]);

        return $this->success(
            ['letter_id' => $letter->id, 'nomor_surat' => $letter->nomor_surat],
            'Pengajuan disetujui dan surat berhasil dibuat.'
        );
    }

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

    public function destroy(PengajuanSurat $pengajuan): JsonResponse
    {
        $pengajuan->delete();
        return $this->noContent();
    }
}
