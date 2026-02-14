<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Site\SiteController;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Visitor\DashboardVisitorController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/cek-koneksi', 
[SiteController::class, 'cekKoneksi'])
->name('site.cek-koneksi');

Auth::routes();

Route::get('/home', 
[App\Http\Controllers\HomeController::class, 'index'])
->name('home');



// AKSES ADMIN (idrole = 1)
Route::get('/admin/dashboard', [DashboardAdminController::class, 'index'])
    ->name('admin.dashboard')
    ->middleware(['auth', 'role:1']);

// AKSES VISITOR (idrole = 2 - default)
Route::get('/visitor/dashboard', [DashboardVisitorController::class, 'index'])
    ->name('visitor.dashboard')
    ->middleware(['auth', 'role:2']);
