<?php

namespace App\Http\Controllers\API\Public;

use App\Http\Controllers\API\BaseController;
use App\Models\JadwalPelayanan;
use Illuminate\Http\JsonResponse;

// JadwalController menangani tampil jadwal pelayanan aktif untuk publik (tanpa autentikasi)
class JadwalController extends BaseController
{
    // API menangani daftar jadwal pelayanan yang aktif diurutkan berdasarkan field urutan
    public function index(): JsonResponse
    {
        $jadwals = JadwalPelayanan::where('aktif', true)
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        return $this->success($jadwals);
    }
}
