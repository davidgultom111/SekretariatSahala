<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\MemberLoginRequest;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemberAuthController extends BaseController
{
    public function login(MemberLoginRequest $request): JsonResponse
    {
        $member = Member::where('id_jemaat', $request->id_jemaat)->first();

        if (!$member || !Hash::check($request->password, $member->password)) {
            return $this->error('ID Jemaat atau password salah', 401);
        }

        if ($member->status_aktif !== 'Aktif') {
            return $this->error('Akun jemaat tidak aktif', 403);
        }

        $token = $member->createToken('api-token')->plainTextToken;

        return $this->success([
            'member' => MemberResource::make($member),
            'token'  => $token,
        ], 'Login berhasil');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->noContent();
    }
}
