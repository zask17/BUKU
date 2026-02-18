<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Site\SiteController;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Visitor\DashboardVisitorController;

Route::get('/', function () {
    return view('welcome');
});

Route::get(
    '/cek-koneksi',
    [SiteController::class, 'cekKoneksi']
)
    ->name('site.cek-koneksi');

Auth::routes();

Route::get(
    '/home',
    [App\Http\Controllers\HomeController::class, 'index']
)
    ->name('home');

// --- RUTE HALAMAN LOGIN ---
// LOGIN
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.process');

// LOGOUT
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// AKSES ADMIN (idrole = 1)
Route::get('/admin/dashboard', [DashboardAdminController::class, 'index'])
    ->name('admin.dashboard')
    ->middleware(['auth', 'role:1']);

Route::get('/admin/kategori', [DashboardAdminController::class, 'kategori'])
    ->name('admin.kategori')
    ->middleware(['auth', 'role:1']);

Route::get('/admin/buku', [DashboardAdminController::class, 'buku'])
    ->name('admin.buku')
    ->middleware(['auth', 'role:1']);


// AKSES VISITOR (idrole = 2 - default)
Route::get('/visitor/dashboard', [DashboardVisitorController::class, 'index'])
    ->name('visitor.dashboard')
    ->middleware(['auth', 'role:2']);

Route::get('/visitor/kategori', [DashboardVisitorController::class, 'kategori'])
    ->name('visitor.kategori')
    ->middleware(['auth', 'role:2']);

Route::get('/visitor/buku', [DashboardVisitorController::class, 'buku'])
    ->name('visitor.buku')
    ->middleware(['auth', 'role:2']);