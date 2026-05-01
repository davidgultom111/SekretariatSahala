<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

// BaseController menyediakan helper response JSON yang dipakai semua controller API
class BaseController extends Controller
{
    // API menangani response sukses dengan data dan pesan (default HTTP 200)
    protected function success(mixed $data = null, string $message = 'Success', int $status = 200): JsonResponse
    {
        $response = ['status' => 'success', 'message' => $message];
        if ($data !== null) {
            $response['data'] = $data;
        }
        return response()->json($response, $status);
    }

    // API menangani response create berhasil (HTTP 201)
    protected function created(mixed $data, string $message = 'Berhasil dibuat'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    // API menangani response tanpa isi untuk operasi delete (HTTP 204)
    protected function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }

    // API menangani response error dengan pesan dan detail validasi opsional
    protected function error(string $message, int $status = 400, array $errors = []): JsonResponse
    {
        $response = ['status' => 'error', 'message' => $message];
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }
        return response()->json($response, $status);
    }
}
