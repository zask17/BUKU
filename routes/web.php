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

use App\Http\Controllers\PdfController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KotaController;



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
Route::get('/auth/google', [LoginController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

// OTP
Route::get('/otp-verify', [LoginController::class, 'showOtpForm'])->name('otp.form');
Route::post('/otp-verify', [LoginController::class, 'verifyOtp'])->name('otp.verify');
Route::post('/otp-resend', [LoginController::class, 'resendOtp'])->name('otp.resend');


// --- GRUP AKSES ADMIN (idrole = 1) ---
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'role:1']], function () {
    // Dashboard
    Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('admin.dashboard');

    // Pengguna
    Route::get('/pengguna', [PenggunaAdminController::class, 'index'])->name('admin.pengguna');

    // Kategori
    Route::get('/kategori', [KategoriAdminController::class, 'index'])->name('admin.kategori.index');
    Route::get('/kategori/create', [KategoriAdminController::class, 'create'])->name('admin.kategori.create');
    Route::post('/kategori/store', [KategoriAdminController::class, 'store'])->name('admin.kategori.store');
    Route::get('/kategori/{id}/edit', [KategoriAdminController::class, 'edit'])->name('admin.kategori.edit');
    Route::put('/kategori/{id}/update', [KategoriAdminController::class, 'update'])->name('admin.kategori.update');
    Route::delete('/kategori/{id}/destroy', [KategoriAdminController::class, 'destroy'])->name('admin.kategori.destroy');

    // Buku
    Route::get('/buku', [BukuAdminController::class, 'index'])->name('admin.buku.index');
    Route::get('/buku/create', [BukuAdminController::class, 'create'])->name('admin.buku.create');
    Route::post('/buku/store', [BukuAdminController::class, 'store'])->name('admin.buku.store');
    Route::get('/buku/{id}/edit', [BukuAdminController::class, 'edit'])->name('admin.buku.edit');
    Route::put('/buku/{id}/update', [BukuAdminController::class, 'update'])->name('admin.buku.update');
    Route::delete('/buku/{id}/destroy', [BukuAdminController::class, 'destroy'])->name('admin.buku.destroy');

    // Barang Baru
    Route::get('/barang-baru', [BarangController::class, 'barangBaru'])->name('admin.barang.baru');
    Route::get('/barang-baru-datatable', [BarangController::class, 'barangBaruDatatable'])->name('admin.barang.datatable');

    // Cari bagian ini di web.php dan ubah:
    Route::get('/kota', [KotaController::class, 'index'])->name('admin.kota.index');
    // Route::get('/barang-baru-datatable', [BarangController::class, 'barangBaruDatatable'])->name('admin.barang.datatable');
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


// --- PDF Routes (bisa diakses admin & visitor) ---
Route::middleware(['auth', 'role:1,2'])->group(function () {
    // Index (menu pilihan sertifikat & undangan)
    Route::get('/generate-pdf', [PdfController::class, 'index'])->name('pdf.index');

    // Form & Proses Sertifikat
    Route::get('/generate-pdf/sertifikat', [PdfController::class, 'sertifikatForm'])->name('pdf.sertifikat.form');
    Route::post('/generate-pdf/sertifikat', [PdfController::class, 'sertifikatPreview'])->name('pdf.sertifikat');

    // Form & Proses Undangan
    Route::get('/generate-pdf/undangan', [PdfController::class, 'undanganForm'])->name('pdf.undangan.form');
    Route::post('/generate-pdf/undangan', [PdfController::class, 'undanganPreview'])->name('pdf.undangan');

    // Preview & Download
    Route::get('/pdf/preview', [PdfController::class, 'preview'])->name('pdf.preview');
    Route::get('/pdf/download', [PdfController::class, 'download'])->name('pdf.download');

    // Cetak PDF Label TnJ 108
    Route::resource('barang', BarangController::class);
    Route::post('/barang/cetak-pdf', [BarangController::class, 'cetakPdf'])->name('barang.cetak');
});


// Route::get('/test-otp-email', function () {
//     $user = \App\Models\User::where('email', 'zaskiarania5@gmail.com')->first();
//     if ($user) {
//         // Generate OTP
//         $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
//         $user->update(['otp' => $otp]);

//         // Kirim email langsung (tanpa panggil controller)
//         \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpMail($otp));

//         return 'OTP telah dikirim ke: ' . $user->email . ' (kode: ' . $otp . ') - Cek Mailtrap/inbox Gmail';
//     }
//     return 'User tidak ditemukan';
// });
