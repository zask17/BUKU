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


    // // Rute untuk Wilayah Axios
    // Route::get('/wilayah/axios', [WilayahController::class, 'indexAxios'])->name('admin.wilayah.index_axios');

    // // Rute untuk Wilayah Ajax (jQuery)
    // Route::get('/wilayah/ajax', [WilayahController::class, 'indexAjax'])->name('admin.wilayah.index_ajax');

    // // Rute API Wilayah
    // Route::post('/wilayah/get-kota', [WilayahController::class, 'getKota'])->name('admin.wilayah.getKota');
    // Route::post('/wilayah/get-kecamatan', [WilayahController::class, 'getKecamatan'])->name('admin.wilayah.getKecamatan');
    // Route::post('/wilayah/get-kelurahan', [WilayahController::class, 'getKelurahan'])->name('admin.wilayah.getKelurahan');