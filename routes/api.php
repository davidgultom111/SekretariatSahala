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

// API menangani login jemaat (publik, tidak perlu token)
Route::prefix('auth')->group(function () {
    Route::post('login', [MemberAuthController::class, 'login']);
});

// API menangani health check untuk memastikan server berjalan
Route::get('health', fn () => response()->json(['status' => 'ok', 'timestamp' => now()->toIso8601String()]));

// API menangani aksi jemaat pada akun sendiri (memerlukan token Sanctum)
Route::middleware('auth:sanctum')->prefix('me')->group(function () {
    // API menangani logout dan mencabut token aktif
    Route::delete('logout', [MemberAuthController::class, 'logout']);

    // API menangani tampil profil jemaat yang sedang login
    Route::get('/', [MemberApiController::class, 'show']);

    // API menangani update biodata jemaat yang sedang login
    Route::put('/', [MemberApiController::class, 'update']);

    // API menangani daftar surat milik jemaat yang sedang login
    Route::get('letters', [MemberApiController::class, 'letters']);

    // API menangani download PDF surat milik jemaat (hanya yang sudah tersimpan)
    Route::get('letters/{letterId}/download', [MemberApiController::class, 'downloadLetter']);
});

// API menangani semua aksi admin (memerlukan token + role admin)
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {

    // API menangani CRUD data jemaat oleh admin (index, store, show, update, destroy)
    Route::apiResource('members', AdminMemberController::class);

    // API menangani CRUD surat gereja oleh admin
    Route::prefix('letters')->group(function () {
        // API menangani daftar semua surat dengan filter dan pagination
        Route::get('/', [AdminLetterController::class, 'index']);

        // API menangani create surat baru
        Route::post('/', [AdminLetterController::class, 'store']);

        // API menangani tampil detail satu surat
        Route::get('/{letter}', [AdminLetterController::class, 'show']);

        // API menangani update data surat
        Route::put('/{letter}', [AdminLetterController::class, 'update']);

        // API menangani hapus surat beserta file PDF-nya
        Route::delete('/{letter}', [AdminLetterController::class, 'destroy']);

        // API menangani download PDF surat (generate on-the-fly jika belum ada)
        Route::get('/{letter}/pdf', [AdminLetterController::class, 'downloadPdf']);
    });
});
