// Route::get('/test-otp-email', function () {
// $user = \App\Models\User::where('email', 'zaskiarania5@gmail.com')->first();
// if ($user) {
// // Generate OTP
// $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
// $user->update(['otp' => $otp]);

// // Kirim email langsung (tanpa panggil controller)
// \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpMail($otp));

// return 'OTP telah dikirim ke: ' . $user->email . ' (kode: ' . $otp . ') - Cek Mailtrap/inbox Gmail';
// }
// return 'User tidak ditemukan';
// });


// // Rute untuk Wilayah Axios
// Route::get('/wilayah/axios', [WilayahController::class, 'indexAxios'])->name('admin.wilayah.index_axios');

// // Rute untuk Wilayah Ajax (jQuery)
// Route::get('/wilayah/ajax', [WilayahController::class, 'indexAjax'])->name('admin.wilayah.index_ajax');

// // Rute API Wilayah
// Route::post('/wilayah/get-kota', [WilayahController::class, 'getKota'])->name('admin.wilayah.getKota');
// Route::post('/wilayah/get-kecamatan', [WilayahController::class, 'getKecamatan'])->name('admin.wilayah.getKecamatan');
// Route::post('/wilayah/get-kelurahan', [WilayahController::class, 'getKelurahan'])->name('admin.wilayah.getKelurahan');

// // --- RUTE MENU CUSTOMER (GUEST) ---
// Route::get('/kantin', [OrderController::class, 'index'])->name('kantin.index');
// Route::post('/kantin/order', [OrderController::class, 'checkout'])->name('kantin.checkout');
// // Callback dari Midtrans
// Route::post('/payment/callback', [PaymentController::class, 'callback']);
// Route::get('/kantin/selesai', function() { return "Pembayaran Berhasil! Pesanan Anda sedang diproses."; });
// Route::get('/kantin/pending', function() { return "Menunggu pembayaran..."; });
// Route::get('/kantin/error', function() { return "Maaf, terjadi kesalahan pembayaran."; });

// Route::prefix('kantin')->name('kantin.')->group(function () {
// Route::get('/', [KantinController::class, 'index'])->name('index');
// Route::post('/checkout',[KantinController::class, 'checkout'])->name('checkout');
// Route::get('/selesai', [KantinController::class, 'selesai'])->name('selesai');
// Route::get('/pending', [KantinController::class, 'pending'])->name('pending');
// Route::get('/gagal', [KantinController::class, 'gagal'])->name('gagal');
// });

// // Webhook Midtrans (tanpa CSRF)
// Route::post('/midtrans/callback', [KantinController::class, 'callback'])
// ->name('midtrans.callback');


// ==================== CUSTOMER ROUTES ====================
Route::prefix('customer')->name('customer.')->group(function () {

    // Daftar Customer
    Route::get('/', [CustomerController::class, 'index'])->name('index');

    // Tambah Customer 1 (Simpan foto sebagai BLOB)
    Route::get('/tambah1', [CustomerController::class, 'create1'])->name('create1');
    Route::post('/tambah1', [CustomerController::class, 'store1'])->name('store1');

    // Tambah Customer 2 (Simpan foto sebagai File Path)
    Route::get('/tambah2', [CustomerController::class, 'create2'])->name('create2');
    Route::post('/tambah2', [CustomerController::class, 'store2'])->name('store2');

    // Edit Customer
    Route::get('/{id}/edit', [CustomerController::class, 'edit'])->name('edit');
    Route::put('/{id}', [CustomerController::class, 'update'])->name('update');

    // Hapus Customer
    Route::delete('/{id}', [CustomerController::class, 'destroy'])->name('destroy');
});


// // --- GRUP AKSES SALES (idrole = 5) ---
// Route::group(['prefix' => 'sales', 'as' => 'sales.', 'middleware' => ['auth', 'role:5']], function () {
//     Route::get('/dashboard', [SalesController::class, 'dashboard'])->name('dashboard');
//     Route::post('/store', [SalesController::class, 'store'])->name('store');
//     Route::get('/barcode/{id}', [SalesController::class, 'findByBarcode'])->name('find-by-barcode');
// });

// --- GRUP AKSES ADMIN (idrole = 1) UNTUK MANAJEMEN TOKO ---
// Route::group(['prefix' => 'toko', 'as' => 'toko.', 'middleware' => ['auth', 'role:1']], function () {
//     Route::get('/', [TokoController::class, 'index'])->name('list');
//     Route::get('/create', [TokoController::class, 'create'])->name('create');
//     Route::post('/store', [TokoController::class, 'store'])->name('store');
//     Route::get('/edit/{id}', [TokoController::class, 'edit'])->name('edit');
//     Route::put('/update/{id}', [TokoController::class, 'update'])->name('update');
//     Route::delete('/delete/{id}', [TokoController::class, 'delete'])->name('delete');
// });