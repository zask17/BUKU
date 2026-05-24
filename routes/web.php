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
Route::get('/cek-koneksi', [SiteController::class, 'cekKoneksi'])->name('site.cek-koneksi');
Route::get('/', [HomeController::class, 'welcome'])->name('welcome');
Route::get('/buku', [BukuGuestController::class, 'index'])->name('buku');
Route::get('/kategori', [KategoriGuestController::class, 'index'])->name('kategori');

// --- RUTE KANTIN GUEST ---
Route::get('/kantin/order-details/{idpesanan}', [KantinController::class, 'getOrderDetails'])->name('kantin.order.details');
Route::get('/kantin', [KantinController::class, 'index'])->name('kantin.index');
Route::post('/kantin/checkout', [KantinController::class, 'checkout'])->name('kantin.checkout');
Route::get('/kantin/selesai/{id}', [KantinController::class, 'selesai'])->name('kantin.selesai');
Route::get('/kantin/pending', [KantinController::class, 'pending'])->name('kantin.pending');
Route::get('/kantin/gagal', [KantinController::class, 'gagal'])->name('kantin.gagal');

// --- ANTRIAN POLI GUEST & REAL-TIME STREAM ENGINE ---
Route::get('/antrian', [AntrianController::class, 'guestIndex'])->name('antrian.guest');
Route::post('/antrian/daftar', [AntrianController::class, 'guestDaftar'])->name('antrian.guest.daftar');
Route::get('/antrian/papan', [AntrianController::class, 'papanIndex'])->name('antrian.papan');
// Mengeluarkan rute stream SSE ke rute umum agar tidak terhalang middleware auth session lock
Route::get('/antrian/sse/stream', [AntrianController::class, 'stream'])->name('antrian.stream');

// --- MIDTRANS WEBHOOK ---
Route::post('/midtrans/callback', [PaymentCallbackController::class, 'callback']);

// --- RUTE WILAYAH ---
Route::get('/wilayah/axios', [WilayahController::class, 'indexAxios'])->name('wilayah.index_axios');
Route::get('/wilayah/ajax', [WilayahController::class, 'indexAjax'])->name('wilayah.index_ajax');
Route::post('/wilayah/get-kota', [WilayahController::class, 'getKota'])->name('wilayah.getKota');
Route::post('/wilayah/get-kecamatan', [WilayahController::class, 'getKecamatan'])->name('wilayah.getKecamatan');
Route::post('/wilayah/get-kelurahan', [WilayahController::class, 'getKelurahan'])->name('wilayah.getKelurahan');

// --- PDF ROUTES ---
Route::get('/generate-pdf', [PdfController::class, 'index'])->name('pdf.index');
Route::get('/generate-pdf/sertifikat', [PdfController::class, 'sertifikatForm'])->name('pdf.sertifikat.form');
Route::post('/generate-pdf/sertifikat', [PdfController::class, 'sertifikatPreview'])->name('pdf.sertifikat');
Route::get('/generate-pdf/undangan', [PdfController::class, 'undanganForm'])->name('pdf.undangan.form');
Route::post('/generate-pdf/undangan', [PdfController::class, 'undanganPreview'])->name('pdf.undangan');
Route::get('/pdf/preview', [PdfController::class, 'preview'])->name('pdf.preview');
Route::get('/pdf/download', [PdfController::class, 'download'])->name('pdf.download');

// --- BARANG & SCANNER ---
Route::get('/barang/scanner', [BarangController::class, 'scannerPage'])->name('barang.scanner');
Route::post('/barang/cek-scan/{id}', [BarangController::class, 'cekBarangScan'])->name('barang.cek_scan');
Route::resource('barang', BarangController::class);
Route::post('/barang/cetak-pdf', [BarangController::class, 'cetakPdf'])->name('barang.cetak');

// --- AUTHENTICATION ---
Auth::routes();
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.process');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Google Login
Route::get('/auth/google', [LoginController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

// OTP
Route::get('/otp-verify', [LoginController::class, 'showOtpForm'])->name('otp.form');
Route::post('/otp-verify', [LoginController::class, 'verifyOtp'])->name('otp.verify');
Route::post('/otp-resend', [LoginController::class, 'resendOtp'])->name('otp.resend');

// --- GRUP AKSES ADMIN (idrole = 1) ---
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth', 'role:1']], function () {
    // Dashboard
    Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('dashboard');

    // Pengguna
    Route::get('/pengguna', [PenggunaAdminController::class, 'index'])->name('pengguna');

    // Kategori
    Route::resource('kategori', KategoriAdminController::class)->except(['show']);
    Route::post('/kategori/store', [KategoriAdminController::class, 'store'])->name('kategori.store');
    Route::put('/kategori/{id}/update', [KategoriAdminController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{id}/destroy', [KategoriAdminController::class, 'destroy'])->name('kategori.destroy');

    // Buku
    Route::resource('buku', BukuAdminController::class)->except(['show']);
    Route::post('/buku/store', [BukuAdminController::class, 'store'])->name('buku.store');
    Route::put('/buku/{id}/update', [BukuAdminController::class, 'update'])->name('buku.update');
    Route::delete('/buku/{id}/destroy', [BukuAdminController::class, 'destroy'])->name('buku.destroy');

    // Keuangan / Toko Geolocation
    Route::prefix('toko')->as('toko.')->group(function () {
        Route::get('/', [TokoController::class, 'index'])->name('index');
        Route::get('/create', [TokoController::class, 'create'])->name('create');
        Route::post('/store', [TokoController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [TokoController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [TokoController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [TokoController::class, 'delete'])->name('delete');
    });

    // Barang Baru & Datatable
    Route::get('/barang-baru/html', [BarangBaruController::class, 'barangBaru'])->name('barang.baru');
    Route::get('/barang-baru/datatable', [BarangBaruController::class, 'barangBaruDatatable'])->name('barang.datatable');

    // Kota & Week 4 Ajax
    Route::get('/kota', [KotaController::class, 'index'])->name('kota.index');
    Route::get('/week4', [WeekEmpatController::class, 'index'])->name('week4.index');
    Route::post('/week4/ajax_submit', [WeekEmpatController::class, 'submit'])->name('week4.ajax_submit');

    // POS (Point of Sales)
    Route::get('/pos/axios', [PosController::class, 'indexAxios'])->name('pos.index_axios');
    Route::get('/pos/ajax', [PosController::class, 'indexAjax'])->name('pos.index_ajax');
    Route::post('/pos/cek-barang', [PosController::class, 'cekBarang'])->name('pos.cek_barang');
    Route::post('/pos/store', [PosController::class, 'store'])->name('pos.store');

    // Customer
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

    // Manajemen Antrian Operator Poli (Backend Terintegrasi)
    Route::prefix('antrian')->as('antrian.')->group(function () {
        Route::get('/', [AntrianController::class, 'adminIndex'])->name('index');
        Route::post('/panggil', [AntrianController::class, 'adminPanggil'])->name('panggil');
        Route::post('/lewatkan', [AntrianController::class, 'adminLewatkan'])->name('lewatkan');
        Route::post('/panggil-terlewat', [AntrianController::class, 'adminPanggilTerlewat'])->name('panggil_terlewat');
    });
});

// --- GRUP AKSES VISITOR (idrole = 2) ---
Route::group(['prefix' => 'visitor', 'middleware' => ['auth', 'role:2']], function () {
    Route::get('/dashboard', [DashboardVisitorController::class, 'index'])->name('visitor.dashboard');
    Route::get('/kategori', [KategoriVisitorController::class, 'index'])->name('visitor.kategori');
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