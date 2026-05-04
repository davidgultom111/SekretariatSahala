<?php

namespace App\Http\Controllers\API\Member;

use App\Http\Controllers\API\BaseController;
use App\Models\Member;
use App\Models\PengajuanSurat;
use App\Services\LetterTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// PengajuanController menangani pengajuan surat oleh jemaat untuk diproses admin
class PengajuanController extends BaseController
{
    // API menangani daftar riwayat pengajuan surat milik jemaat yang sedang login
    public function index(Request $request): JsonResponse
    {
        $pengajuans = PengajuanSurat::where('member_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get(['id', 'letter_type', 'tipe_surat', 'status', 'letter_id', 'catatan', 'created_at']);

        return $this->success($pengajuans);
    }

    // API menangani kirim pengajuan surat baru dengan validasi field sesuai tipe surat
    public function store(Request $request): JsonResponse
    {
        $allTypes = LetterTemplateService::getLetterTypes();
        $member   = $request->user();

        $request->validate([
            'letter_type' => 'required|in:' . implode(',', array_keys($allTypes)),
        ]);

        $type = $request->letter_type;

        $typeRules = match ($type) {
            'surat_tugas_pelayanan' => [
                'tgl_mulai_tugas' => 'required|date',
                'tgl_akhir_tugas' => 'required|date|after_or_equal:tgl_mulai_tugas',
                'tujuan_tugas'    => 'required|string|max:500',
            ],
            'surat_pengantar' => [
                'keterangan' => 'required|string|max:300',
            ],
            'surat_keterangan_jemaat_aktif' => [
                'tahun_bergabung' => 'required|integer|min:1900|max:' . now()->year,
            ],
            'surat_nilai_sekolah' => [
                'asal_sekolah' => 'required|string|max:100',
                'kelas'        => 'required|string|max:20',
                'semester'     => 'required|string|max:10',
            ],
            'surat_pengajuan_baptisan' => [],
            'surat_pengajuan_penyerahan_anak' => [
                'nama_ayah'         => 'required|string|max:100',
                'nama_ibu'          => 'required|string|max:100',
                'nama_anak'         => 'required|string|max:100',
                'tempat_lahir_anak' => 'required|string|max:100',
                'tanggal_lahir_anak' => 'required|date',
            ],
            'surat_pengajuan_pernikahan' => [
                'id_jemaat_pria'    => 'required|string|exists:members,id_jemaat',
                'id_jemaat_wanita'  => 'required|string|exists:members,id_jemaat',
                'tanggal_pernikahan' => 'required|date|after:today',
            ],
            default => [],
        };

        $request->validate($typeRules);

        $data = [
            'member_id'   => $member->id,
            'letter_type' => $type,
            'tipe_surat'  => $allTypes[$type],
            'status'      => 'Dalam Proses',
            'keterangan'        => $request->keterangan,
            'tgl_mulai_tugas'   => $request->tgl_mulai_tugas,
            'tgl_akhir_tugas'   => $request->tgl_akhir_tugas,
            'tujuan_tugas'      => $request->tujuan_tugas,
            'tahun_bergabung'   => $request->tahun_bergabung,
            'asal_sekolah'      => $request->asal_sekolah,
            'kelas'             => $request->kelas,
            'semester'          => $request->semester,
            'nilai'             => 90,
            'nama_ayah'         => $request->nama_ayah,
            'nama_ibu'          => $request->nama_ibu,
            'nama_anak'         => $request->nama_anak,
            'tempat_lahir_anak' => $request->tempat_lahir_anak,
            'tanggal_lahir_anak' => $request->tanggal_lahir_anak,
            'tanggal_pernikahan' => $request->tanggal_pernikahan,
        ];

        if ($type === 'surat_pengajuan_pernikahan') {
            $pria   = Member::where('id_jemaat', $request->id_jemaat_pria)->firstOrFail();
            $wanita = Member::where('id_jemaat', $request->id_jemaat_wanita)->firstOrFail();
            $data['member_pria_id']   = $pria->id;
            $data['member_wanita_id'] = $wanita->id;
        }

        $pengajuan = PengajuanSurat::create($data);

        return $this->created($pengajuan, 'Pengajuan surat berhasil dikirim dan sedang diproses.');
    }
}
