<?php

namespace App\Http\Controllers\API\Public;

use App\Http\Controllers\API\BaseController;
use App\Models\GaleriFoto;
use Illuminate\Http\JsonResponse;

class GaleriController extends BaseController
{
    public function index(): JsonResponse
    {
        $galeri = GaleriFoto::orderBy('urutan')->orderBy('id')->get();

        return $this->success($galeri);
    }
}
