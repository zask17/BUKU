<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Site\SiteController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Visitor\DashboardVisitorController;

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

// --- GRUP AKSES ADMIN (idrole = 1) ---
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'role:1']], function () {
    // Dashboard
    Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('admin.dashboard');

    // CRUD Kategori
    Route::get('/kategori', [DashboardAdminController::class, 'kategori'])->name('admin.kategori');
    Route::post('/kategori', [DashboardAdminController::class, 'kategoriStore'])->name('admin.kategori.store');
    Route::put('/kategori/{id}', [DashboardAdminController::class, 'kategoriUpdate'])->name('admin.kategori.update');
    Route::delete('/kategori/{id}', [DashboardAdminController::class, 'kategoriDestroy'])->name('admin.kategori.destroy');

    // CRUD Buku
    Route::get('/buku', [DashboardAdminController::class, 'buku'])->name('admin.buku');
    Route::post('/buku', [DashboardAdminController::class, 'bukuStore'])->name('admin.buku.store');
    Route::put('/buku/{id}', [DashboardAdminController::class, 'bukuUpdate'])->name('admin.buku.update');
    Route::delete('/buku/{id}', [DashboardAdminController::class, 'bukuDestroy'])->name('admin.buku.destroy');

    // Manajemen Pengguna
    Route::get('/pengguna', [DashboardAdminController::class, 'pengguna'])->name('admin.pengguna');
});

// --- GRUP AKSES VISITOR (idrole = 2) ---
Route::group(['prefix' => 'visitor', 'middleware' => ['auth', 'role:2']], function () {
    Route::get('/dashboard', [DashboardVisitorController::class, 'index'])->name('visitor.dashboard');
    Route::get('/kategori', [DashboardVisitorController::class, 'kategori'])->name('visitor.kategori');
    Route::get('/buku', [DashboardVisitorController::class, 'buku'])->name('visitor.buku');
});