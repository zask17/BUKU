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
