<?php

namespace App\Http\Controllers\API\Public;

use App\Http\Controllers\API\BaseController;
use App\Models\Pengumuman;
use Illuminate\Http\JsonResponse;

class PengumumanController extends BaseController
{
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
