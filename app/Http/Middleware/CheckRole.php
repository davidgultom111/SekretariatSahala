<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // Jika Anda ingin Admin (web) dan Member (sanctum) dibedakan:
public function handle(Request $request, Closure $next, ...$roles): Response
{
    // Jika tidak login
    if (!auth('sanctum')->check()) {
        return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
    }

    $user = auth('sanctum')->user();
    
    // Asumsi: Admin memiliki kolom 'is_admin' atau 'role'
    // Sesuaikan dengan struktur database Anda
    $userRole = $user->role ?? 'member'; 

    if (!in_array($userRole, $roles)) {
        return response()->json(['status' => 'error', 'message' => 'Forbidden'], 403);
    }

    return $next($request);
}
}