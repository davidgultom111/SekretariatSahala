<?php

use App\Http\Controllers\API\MemberAuthController;
use App\Http\Controllers\API\MemberApiController;
use App\Http\Controllers\API\Member\PengajuanController as MemberPengajuanController;
use App\Http\Controllers\API\Admin\PengajuanController as AdminPengajuanController;
use App\Http\Controllers\API\Admin\MemberController as AdminMemberController;
use App\Http\Controllers\API\Admin\LetterController as AdminLetterController;
use App\Http\Controllers\API\Admin\JadwalController as AdminJadwalController;
use App\Http\Controllers\API\Admin\GaleriController as AdminGaleriController;
use App\Http\Controllers\API\Admin\PengumumanController as AdminPengumumanController;
use App\Http\Controllers\API\Public\JadwalController as PublicJadwalController;
use App\Http\Controllers\API\Public\GaleriController as PublicGaleriController;
use App\Http\Controllers\API\Public\PengumumanController as PublicPengumumanController;
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

// Endpoint publik (tanpa auth) untuk data beranda website
Route::prefix('public')->group(function () {
    Route::get('/jadwal',      [PublicJadwalController::class,      'index']);
    Route::get('/galeri',      [PublicGaleriController::class,      'index']);
    Route::get('/pengumuman',  [PublicPengumumanController::class,  'index']);
});

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

    // API menangani pengajuan surat oleh jemaat
    Route::get('pengajuan',  [MemberPengajuanController::class, 'index']);
    Route::post('pengajuan', [MemberPengajuanController::class, 'store']);
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

    // Jadwal pelayanan (CRUD tanpa show)
    Route::get('/jadwal',          [AdminJadwalController::class, 'index']);
    Route::post('/jadwal',         [AdminJadwalController::class, 'store']);
    Route::put('/jadwal/{jadwal}', [AdminJadwalController::class, 'update']);
    Route::delete('/jadwal/{jadwal}', [AdminJadwalController::class, 'destroy']);

    // Galeri foto (upload + hapus saja)
    Route::get('/galeri',           [AdminGaleriController::class, 'index']);
    Route::post('/galeri',          [AdminGaleriController::class, 'store']);
    Route::delete('/galeri/{galeri}', [AdminGaleriController::class, 'destroy']);

    // Pengumuman (CRUD tanpa show)
    Route::get('/pengumuman',              [AdminPengumumanController::class, 'index']);
    Route::post('/pengumuman',             [AdminPengumumanController::class, 'store']);
    Route::put('/pengumuman/{pengumuman}', [AdminPengumumanController::class, 'update']);
    Route::delete('/pengumuman/{pengumuman}', [AdminPengumumanController::class, 'destroy']);

    // Pengajuan surat jemaat (review, setujui, tolak, hapus)
    Route::get('/pengajuan',                          [AdminPengajuanController::class, 'index']);
    Route::get('/pengajuan/{pengajuan}',              [AdminPengajuanController::class, 'show']);
    Route::put('/pengajuan/{pengajuan}/setujui',      [AdminPengajuanController::class, 'approve']);
    Route::put('/pengajuan/{pengajuan}/tolak',        [AdminPengajuanController::class, 'reject']);
    Route::delete('/pengajuan/{pengajuan}',           [AdminPengajuanController::class, 'destroy']);
});
