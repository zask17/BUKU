<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

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
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // 1. Validasi input email dan password
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // 2. Ambil data user beserta relasi roles-nya
        $user = User::with('role')
            ->where('email', $request->email)
            ->first();

        // Cek apakah user ditemukan
        if (!$user) {
            return redirect()->back()
                ->withErrors(['email' => 'Email tidak ditemukan.'])
                ->withInput();
        }

        // Cek password menggunakan Hash::check()
        if (!Hash::check($request->password, $user->password)) {
            return redirect()->back()
                ->withErrors(['password' => 'Password salah.'])
                ->withInput();
        }

        // // Ambil role pertama yang aktif
        // $activeRole = $user->roles->first();

        // 3. Login user ke session Laravel
        Auth::login($user);

        // 4. Simpan session user kustom
        $request->session()->put([
            'user_id' => $user->iduser,
            'user_name' => $user->nama_user,
            'user_email' => $user->email,
            'user_role' => $user->idrole,
            'user_role_name' => $user->role->nama_role ?? 'Guest'
        ]);

        // 5. Logika redirect berdasarkan Role ID
        // idrole 1: Admin, idrole 2: Visitor (Default)

        if ($user->idrole == 1) {
            return redirect()->route('admin.dashboard')->with('Success', 'Selamat datang Admin!');
        } else {
            return redirect()->route('visitor.dashboard')->with('Success', 'Login berhasil!');
        }
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('Success', 'Logout berhasil!');
    }

        protected function loggedOut(Request $request)
    {
        return redirect('/')->with('success', 'Logout berhasil!');
    }
}
