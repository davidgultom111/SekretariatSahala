<?php

namespace App\Http\Controllers\API\Public;

use App\Http\Controllers\API\BaseController;
use App\Models\GaleriFoto;
use Illuminate\Http\JsonResponse;

// GaleriController menangani tampil foto galeri gereja untuk publik (tanpa autentikasi)
class GaleriController extends BaseController
{
    // API menangani daftar semua foto galeri diurutkan berdasarkan field urutan
    public function index(): JsonResponse
    {
        $galeri = GaleriFoto::orderBy('urutan')->orderBy('id')->get();

        return $this->success($galeri);
    }
}
