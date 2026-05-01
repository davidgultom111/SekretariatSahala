<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// CheckRole menangani pengecekan role jemaat sebelum mengakses route tertentu
class CheckRole
{
    // API menangani validasi role pengguna (misal: hanya admin yang boleh akses)
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Tolak request jika tidak ada token aktif (belum login)
        if (!auth('sanctum')->check()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $user = auth('sanctum')->user();

        // Ambil role dari kolom 'role' pada tabel members, default ke 'member'
        $userRole = $user->role ?? 'member';

        // Tolak akses jika role pengguna tidak termasuk dalam role yang diizinkan
        if (!in_array($userRole, $roles)) {
            return response()->json(['status' => 'error', 'message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
