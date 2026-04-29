<?php

use App\Http\Controllers\API\MemberAuthController;
use App\Http\Controllers\API\MemberApiController;
use App\Http\Controllers\API\Admin\MemberController as AdminMemberController;
use App\Http\Controllers\API\Admin\LetterController as AdminLetterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — GPdI Sekretariat
|--------------------------------------------------------------------------
*/

// --- Public ---
Route::prefix('auth')->group(function () {
    Route::post('login', [MemberAuthController::class, 'login']);
});

// --- Health check (Public agar bisa dicek koneksinya) ---
Route::get('health', fn () => response()->json(['status' => 'ok', 'timestamp' => now()->toIso8601String()]));

// --- Member self-service (Memerlukan Token) ---
Route::middleware('auth:sanctum')->prefix('me')->group(function () {
    Route::delete('logout', [MemberAuthController::class, 'logout']);
    Route::get('/', [MemberApiController::class, 'show']);
    Route::put('/', [MemberApiController::class, 'update']);
    Route::get('letters', [MemberApiController::class, 'letters']);
    Route::get('letters/{letterId}/download', [MemberApiController::class, 'downloadLetter']);
});

// --- Admin (Memerlukan Token & Role Admin) ---
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    // API Resource sudah mencakup index, store, show, update, destroy
    Route::apiResource('members', AdminMemberController::class);

    // Letters CRUD
    Route::prefix('letters')->group(function () {
        Route::get('/', [AdminLetterController::class, 'index']);
        Route::post('/', [AdminLetterController::class, 'store']);
        Route::get('/{letter}', [AdminLetterController::class, 'show']);
        Route::put('/{letter}', [AdminLetterController::class, 'update']); // Tambahkan jika ada edit
        Route::delete('/{letter}', [AdminLetterController::class, 'destroy']);
        Route::get('/{letter}/pdf', [AdminLetterController::class, 'downloadPdf']);
    });
});