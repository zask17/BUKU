<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Role;
use App\Mail\OtpMail;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
// use Illuminate\Contracts\View\View;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except([
            'logout',
            'verifyOtp',
            'showOtpForm',
            'redirectToGoogle',
            'handleGoogleCallback'
        ]);
        $this->middleware('auth')->only('logout');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function showLoginForm(): \Illuminate\Contracts\View\View
    {
        return view('auth.login');
    }

    // Login (email + password) -> OTP -> Verifikasi OTP -> Login berhasil
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // Pesan error dipisah seperti kode aslimu
        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.'])->withInput();
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password salah.'])->withInput();
        }

        // Simpan ID user sementara di session untuk proses OTP
        session(['pending_user_id' => $user->iduser]);

        // Kirim OTP
        $this->sendOtp($user);

        return redirect()->route('otp.form');
    }

    /**
     * Redirect ke halaman login Google
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Callback setelah autentikasi Google
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Cari user berdasarkan id_google atau email
            $user = User::where('id_google', $googleUser->id)
                ->orWhere('email', $googleUser->email)
                ->first();

            if ($user) {
                if (!$user->id_google) {
                    $user->update(['id_google' => $googleUser->id]);
                }
            } else {
                $user = User::create([
                    'nama_user'   => $googleUser->name ?? 'User Google',
                    'email'       => $googleUser->email,
                    'id_google'   => $googleUser->id,
                    'password'    => Hash::make(Str::random(16)),
                    'idrole'      => 2, // default visitor
                ]);
            }

            session(['pending_user_id' => $user->iduser]);

            $this->sendOtp($user);

            // dd('OTP dikirim ke: ' . $user->email, 'Kode OTP: ' . $user->otp);

            return redirect()->route('otp.form');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Gagal login dengan Google.');
        }
    }

    /**
     * Tampilkan halaman input OTP
     */
    public function showOtpForm()
    {
        if (!session('pending_user_id')) {
            return redirect('/login');
        }

        return view('emails.otp-verify');
    }

    /**
     * Verifikasi kode OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|size:6',
        ]);

        $user = User::find(session('pending_user_id'));

        if (!$user || $user->otp !== $request->otp) {
            return back()->withErrors(['otp' => 'Kode OTP salah atau kadaluarsa.']);
        }

        Auth::login($user);

        $user->update(['otp' => null]);
        session()->forget('pending_user_id');

        if ($user->idrole == 1) {
            return redirect()->route('admin.dashboard')
                ->with('success', 'Selamat datang Admin!');
        }

        return redirect()->route('visitor.dashboard')
            ->with('success', 'Login berhasil!');

    }

    /**
     * Kirim ulang kode OTP
     */
    public function resendOtp(Request $request)
    {
        $userId = session('pending_user_id');

        if (!$userId) {
            return redirect()->route('login')->withErrors(['email' => 'Sesi habis, silakan login kembali.']);
        }

        $user = User::find($userId);

        if ($user) {
            $this->sendOtp($user);
            return back()->with('success', 'Kode OTP baru telah dikirim ke email Anda.');
        }

        return redirect()->route('login');
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logout berhasil!');
    }

    /**
     * Fungsi helper: generate OTP 6 digit, simpan ke kolom otp, kirim email
     */
    private function sendOtp(User $user)
    {
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update(['otp' => $otp]);

        Mail::to($user->email)->send(new OtpMail($otp));
    }

    //     protected function loggedOut(Request $request)
    // {
    //     return redirect('/')->with('success', 'Logout berhasil!');
    // }
}
