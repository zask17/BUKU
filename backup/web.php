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

// ... (Rute lainnya tetap sama)