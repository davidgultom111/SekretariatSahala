<?php

namespace App\Http\Controllers\API\Public;

use App\Http\Controllers\API\BaseController;
use App\Models\Pengumuman;
use Illuminate\Http\JsonResponse;

// PengumumanController menangani tampil pengumuman aktif yang masih berlaku untuk publik (tanpa autentikasi)
class PengumumanController extends BaseController
{
    // API menangani daftar pengumuman aktif yang tanggal_mulai sudah lewat dan tanggal_akhir belum habis
    public function index(): JsonResponse
    {
        $pengumuman = Pengumuman::where('aktif', true)
            ->where('tanggal_mulai', '<=', today())
            ->where(function ($q) {
                $q->whereNull('tanggal_akhir')->orWhere('tanggal_akhir', '>=', today());
            })
            ->orderByDesc('tanggal_mulai')
            ->orderByDesc('id')
            ->get();

        return $this->success($pengumuman);
    }
}
