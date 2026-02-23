<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Site\SiteController;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\EmailVerificationController;

use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\PenggunaAdminController;
use App\Http\Controllers\Admin\KategoriAdminController;
use App\Http\Controllers\Admin\BukuAdminController;

use App\Http\Controllers\Visitor\DashboardVisitorController;
use App\Http\Controllers\Visitor\KategoriVisitorController;
use App\Http\Controllers\Visitor\BukuVisitorController;


// --- RUTE UMUM ---
Route::get('/', function () {
    return view('welcome');
});

Route::get('/cek-koneksi', [SiteController::class, 'cekKoneksi'])->name('site.cek-koneksi');

// --- AUTHENTICATION ---
Auth::routes();

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.process');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
// Ganti dari POST jadi GET (ini lagi eror aja, nanti balik ke POST)
// Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Google Login
Route::get('/auth/google', [App\Http\Controllers\Auth\LoginController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [App\Http\Controllers\Auth\LoginController::class, 'handleGoogleCallback']);

// OTP
Route::get('/otp-verify', [App\Http\Controllers\Auth\LoginController::class, 'showOtpForm'])->name('otp.form');
Route::post('/otp-verify', [App\Http\Controllers\Auth\LoginController::class, 'verifyOtp'])->name('otp.verify');


// --- GRUP AKSES ADMIN (idrole = 1) ---
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'role:1']], function () {
    // Dashboard
    Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('admin.dashboard');

    // Pengguna
    Route::get('/pengguna', [PenggunaAdminController::class, 'index'])->name('admin.pengguna');

    // Kategori
    Route::get('/kategori', [KategoriAdminController::class, 'index'])->name('admin.kategori');
    Route::post('/kategori', [KategoriAdminController::class, 'store'])->name('admin.kategori.store');
    Route::put('/kategori/{id}', [KategoriAdminController::class, 'update'])->name('admin.kategori.update');
    Route::delete('/kategori/{id}', [KategoriAdminController::class, 'destroy'])->name('admin.kategori.destroy');

    // Buku
    Route::get('/buku', [BukuAdminController::class, 'index'])->name('admin.buku');
    Route::post('/buku', [BukuAdminController::class, 'store'])->name('admin.buku.store');
    Route::put('/buku/{id}', [BukuAdminController::class, 'update'])->name('admin.buku.update');
    Route::delete('/buku/{id}', [BukuAdminController::class, 'destroy'])->name('admin.buku.destroy');
});

// --- GRUP AKSES VISITOR (idrole = 2) ---
Route::group(['prefix' => 'visitor', 'middleware' => ['auth', 'role:2']], function () {
    // Dashboard
    Route::get('/dashboard', [DashboardVisitorController::class, 'index'])->name('visitor.dashboard');

    // Kategori
    Route::get('/kategori', [KategoriVisitorController::class, 'index'])->name('visitor.kategori');

    // Buku
    Route::get('/buku', [BukuVisitorController::class, 'index'])->name('visitor.buku');
});