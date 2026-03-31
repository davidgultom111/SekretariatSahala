<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\LetterController;

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Member Routes
    Route::resource('member', MemberController::class);

    // Letter Routes
    Route::get('/letter/types', [LetterController::class, 'types'])->name('letter.types');
    Route::get('/letter/create/{type}', [LetterController::class, 'create'])->name('letter.create');
    Route::resource('letter', LetterController::class, ['except' => ['create']]);
    Route::get('/letter/search', [LetterController::class, 'search'])->name('letter.search');
});

Route::redirect('/', '/dashboard');
