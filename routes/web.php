<?php

use App\Http\Controllers\Admin\BukuAdminController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\KategoriAdminController;
use App\Http\Controllers\Admin\KotaController;
use App\Http\Controllers\Admin\PenggunaAdminController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\WeekEmpatController;
use App\Http\Controllers\AntrianController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BarangBaruController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\Guest\BukuGuestController;
use App\Http\Controllers\Guest\KategoriGuestController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KantinController;
use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\Site\SiteController;
use App\Http\Controllers\TokoController;
use App\Http\Controllers\Vendor\DashboardVendorController;
use App\Http\Controllers\Vendor\MenuController;
use App\Http\Controllers\Visitor\BukuVisitorController;
use App\Http\Controllers\Visitor\DashboardVisitorController;
use App\Http\Controllers\Visitor\KategoriVisitorController;
use App\Http\Controllers\WilayahController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// --- RUTE UMUM ---
// Route::get('/', function () {return view('welcome');});
Route::get('/cek-koneksi', [SiteController::class, 'cekKoneksi'])->name('site.cek-koneksi');

Route::get('/', [HomeController::class, 'welcome'])->name('welcome');
Route::get('/buku', [BukuGuestController::class, 'index'])->name('buku');
Route::get('/kategori', [KategoriGuestController::class, 'index'])->name('kategori');

// --- RUTE KANTIN GUEST ---
Route::get('/kantin/order-details/{idpesanan}', [App\Http\Controllers\KantinController::class, 'getOrderDetails'])
    ->name('kantin.order.details');
Route::get('/kantin', [KantinController::class, 'index'])->name('kantin.index');
Route::post('/kantin/checkout', [KantinController::class, 'checkout'])->name('kantin.checkout');
Route::get('/kantin/selesai/{id}', [KantinController::class, 'selesai'])->name('kantin.selesai');
Route::get('/kantin/pending', [KantinController::class, 'pending'])->name('kantin.pending');
Route::get('/kantin/gagal', [KantinController::class, 'gagal'])->name('kantin.gagal');

Route::get('/antrian', [AntrianController::class, 'guestIndex'])
    ->name('antrian.guest');

Route::post('/antrian/daftar', [AntrianController::class, 'guestDaftar'])
    ->name('antrian.daftar');

// Route::get('/guest', [AntrianController::class, 'guestIndex'])->name('antrian.guest');
// Route::post('/guest/daftar', [AntrianController::class, 'guestDaftar'])->name('antrian.daftar');

// Endpoint Khusus Aliran Data Real-Time SSE Stream
Route::get('/antrian/stream', [AntrianController::class, 'stream'])
    ->name('antrian.stream');
// Route::get('/sse/antrian', [AntrianController::class, 'stream'])->name('antrian.stream');

// Webhook Midtrans (Pastikan URL ini didaftarkan di Dashboard Midtrans: https://namadomain.com/midtrans/callback)
// Route::post('/midtrans/callback', [PaymentCallbackController::class, 'callback'])->name('midtrans.callback');
Route::post('/midtrans/callback', [PaymentCallbackController::class, 'callback']);

// RUTE WILAYAH (Bisa diakses semua pengguna)
Route::get('/wilayah/axios', [WilayahController::class, 'indexAxios'])->name('wilayah.index_axios');
Route::get('/wilayah/ajax', [WilayahController::class, 'indexAjax'])->name('wilayah.index_ajax');

// Rute API (Digunakan bersama oleh AJAX & Axios)
Route::post('/wilayah/get-kota', [WilayahController::class, 'getKota'])->name('wilayah.getKota');
Route::post('/wilayah/get-kecamatan', [WilayahController::class, 'getKecamatan'])->name('wilayah.getKecamatan');
Route::post('/wilayah/get-kelurahan', [WilayahController::class, 'getKelurahan'])->name('wilayah.getKelurahan');

// --- PDF Routes (bisa diakses semua pengguna) ---
// Route::middleware(['auth', 'role:1,2'])->group(function () {
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
Route::get('/barang/scanner', [BarangController::class, 'scannerPage'])->name('barang.scanner');
Route::post('/barang/cek-scan/{id}', [BarangController::class, 'cekBarangScan'])->name('barang.cek_scan');
Route::resource('barang', BarangController::class);
Route::post('/barang/cetak-pdf', [BarangController::class, 'cetakPdf'])->name('barang.cetak');
// });



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
// Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'role:1']], function () {
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth', 'role:1']], function () {
    // Dashboard
    Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('dashboard');

    // Pengguna
    Route::get('/pengguna', [PenggunaAdminController::class, 'index'])->name('pengguna');

    // Kategori
    Route::get('/kategori', [KategoriAdminController::class, 'index'])->name('kategori.index');
    Route::get('/kategori/create', [KategoriAdminController::class, 'create'])->name('kategori.create');
    Route::post('/kategori/store', [KategoriAdminController::class, 'store'])->name('kategori.store');
    Route::get('/kategori/{id}/edit', [KategoriAdminController::class, 'edit'])->name('kategori.edit');
    Route::put('/kategori/{id}/update', [KategoriAdminController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{id}/destroy', [KategoriAdminController::class, 'destroy'])->name('kategori.destroy');

    // Buku
    Route::get('/buku', [BukuAdminController::class, 'index'])->name('buku.index');
    Route::get('/buku/create', [BukuAdminController::class, 'create'])->name('buku.create');
    Route::post('/buku/store', [BukuAdminController::class, 'store'])->name('buku.store');
    Route::get('/buku/{id}/edit', [BukuAdminController::class, 'edit'])->name('buku.edit');
    Route::put('/buku/{id}/update', [BukuAdminController::class, 'update'])->name('buku.update');
    Route::delete('/buku/{id}/destroy', [BukuAdminController::class, 'destroy'])->name('buku.destroy');

    // Manajemen Toko (Modul 9 Geolocation)
    Route::prefix('toko')->as('toko.')->group(function () {
        Route::get('/', [TokoController::class, 'index'])->name('index');
        Route::get('/create', [TokoController::class, 'create'])->name('create');
        Route::post('/store', [TokoController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [TokoController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [TokoController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [TokoController::class, 'delete'])->name('delete');
    });

    // Barang Baru
    Route::get('/barang-baru/html', [BarangBaruController::class, 'barangBaru'])->name('barang.baru');
    Route::get('/barang-baru/datatable', [BarangBaruController::class, 'barangBaruDatatable'])->name('barang.datatable');

    // Kota
    Route::get('/kota', [KotaController::class, 'index'])->name('kota.index');

    // Ajax Week4
    Route::get('/week4', [WeekEmpatController::class, 'index'])->name('week4.index');
    Route::post('/week4/ajax_submit', [WeekEmpatController::class, 'submit'])->name('week4.ajax_submit');

    // Rute untuk POS versi Axios
    Route::get('/pos/axios', [PosController::class, 'indexAxios'])->name('pos.index_axios');

    // Rute untuk POS versi Ajax (jQuery)
    Route::get('/pos/ajax', [PosController::class, 'indexAjax'])->name('pos.index_ajax');

    // Rute Store (Digunakan oleh keduanya)
    Route::post('/pos/cek-barang', [PosController::class, 'cekBarang'])->name('pos.cek_barang');
    Route::post('/pos/store', [PosController::class, 'store'])->name('pos.store');

    Route::prefix('customer')->as('customer.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/tambah1', [CustomerController::class, 'create1'])->name('create1');
        Route::post('/tambah1', [CustomerController::class, 'store1'])->name('store1');
        Route::get('/tambah2', [CustomerController::class, 'create2'])->name('create2');
        Route::post('/tambah2', [CustomerController::class, 'store2'])->name('store2');
        Route::get('/edit/{id}', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [CustomerController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [CustomerController::class, 'destroy'])->name('destroy');
    });

    // Antrian
    Route::prefix('antrian')->as('antrian.')->group(function () {
        Route::get('/', [AntrianController::class, 'adminIndex'])->name('index');
        Route::post('/panggil', [AntrianController::class, 'panggilNext'])->name('panggil');
        Route::post('/lewatkan', [AntrianController::class, 'lewatkanAntrian'])->name('lewatkan');
        Route::post('/panggil-terlewat', [AntrianController::class, 'panggilTerlewat'])->name('panggil_terlewat');
        Route::get('/papan', [AntrianController::class, 'papanIndex'])->name('papan');
    });
    // Route::get('/antrian', [AntrianController::class, 'adminIndex'])->name('antrian.admin');
    // Route::post('/antrian/panggil', [AntrianController::class, 'panggilNext'])->name('antrian.panggil');
    // Route::post('/antrian/lewatkan', [AntrianController::class, 'lewatkanAntrian'])->name('antrian.lewatkan');
    // Route::post('/antrian/panggil-terlewat', [AntrianController::class, 'panggilTerlewat'])->name('antrian.panggil_terlewat');
    // Route::get('/antrian/papan', [AntrianController::class, 'papanIndex'])->name('antrian.papan');
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



// --- GRUP AKSES VENDOR (idrole = 3) ---
Route::group(['prefix' => 'vendor', 'as' => 'vendor.', 'middleware' => ['auth', 'role:3']], function () {
    Route::get('/dashboard', [DashboardVendorController::class, 'index'])->name('dashboard');
    Route::get('/pesanan', [DashboardVendorController::class, 'pesanan'])->name('pesanan');
    Route::get('/scanner', [DashboardVendorController::class, 'scannerQRCode'])->name('scanner');
    Route::resource('menu', MenuController::class);
});



// --- GRUP AKSES SALES (idrole = 5) ---
Route::group(['prefix' => 'sales', 'as' => 'sales.', 'middleware' => ['auth', 'role:5']], function () {
    Route::get('/dashboard', [SalesController::class, 'index'])->name('dashboard');
    Route::get('/barcode/{id}', [SalesController::class, 'findByBarcode'])->name('find-by-barcode');
    Route::post('/store-visit', [SalesController::class, 'storeVisit'])->name('storeVisit');
});
