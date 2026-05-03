<?php

namespace App\Http\Controllers\API\Public;

use App\Http\Controllers\API\BaseController;
use App\Models\JadwalPelayanan;
use Illuminate\Http\JsonResponse;

class JadwalController extends BaseController
{
    public function index(): JsonResponse
    {
        $jadwals = JadwalPelayanan::where('aktif', true)
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        return $this->success($jadwals);
    }
}
