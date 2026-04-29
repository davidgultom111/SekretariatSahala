<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    protected function success(mixed $data = null, string $message = 'Success', int $status = 200): JsonResponse
    {
        $response = ['status' => 'success', 'message' => $message];
        if ($data !== null) {
            $response['data'] = $data;
        }
        return response()->json($response, $status);
    }

    protected function created(mixed $data, string $message = 'Berhasil dibuat'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    protected function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }

    protected function error(string $message, int $status = 400, array $errors = []): JsonResponse
    {
        $response = ['status' => 'error', 'message' => $message];
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }
        return response()->json($response, $status);
    }
}
