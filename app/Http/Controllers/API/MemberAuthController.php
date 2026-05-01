<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\MemberLoginRequest;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

// MemberAuthController menangani autentikasi jemaat (login dan logout)
class MemberAuthController extends BaseController
{
    // API menangani login jemaat menggunakan id_jemaat dan password
    public function login(MemberLoginRequest $request): JsonResponse
    {
        // Cari jemaat berdasarkan id_jemaat
        $member = Member::where('id_jemaat', $request->id_jemaat)->first();

        // Validasi kecocokan password
        if (!$member || !Hash::check($request->password, $member->password)) {
            return $this->error('ID Jemaat atau password salah', 401);
        }

        // Tolak login jika status jemaat bukan Aktif
        if ($member->status_aktif !== 'Aktif') {
            return $this->error('Akun jemaat tidak aktif', 403);
        }

        // Buat token Sanctum baru untuk sesi ini
        $token = $member->createToken('api-token')->plainTextToken;

        return $this->success([
            'member' => MemberResource::make($member),
            'token'  => $token,
        ], 'Login berhasil');
    }

    // API menangani logout dengan mencabut token yang sedang aktif
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->noContent();
    }
}
