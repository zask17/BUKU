@extends('layouts.guest.main-auth')

@section('title-page', 'Login')

@section('content')
    <div class="content-wrapper d-flex align-items-center auth vh-100">
        <div class="row w-100 mx-0 flex-grow">
            <div class="col-lg-4 mx-auto">
                <div class="auth-form-light text-left p-5 shadow-sm rounded-4 position-relative">
                    {{-- <div class="auth-form-light text-left p-5 shadow rounded-4 bg-white position-relative"></div> --}}

                    <div class="position-absolute" style="top: 25px; left: 25px;">
                        <a href="{{ route('welcome') }}"
                            class="text-muted text-decoration-none small d-flex align-items-center hover-opacity">
                            <i class="mdi mdi-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                    
                    <div class="brand-logo text-center mb-4">
                        <img src="{{ asset('assets/images/logo.svg') }}" alt="logo">
                    </div>
                    <h4 class="text-center">Halo! Selamat datang</h4>
                    <h6 class="font-weight-light text-center">Masuk untuk melanjutkan.</h6>

                    <form class="pt-3" id="loginForm" method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label small fw-semibold text-muted">Alamat Email</label>
                            <input type="email" name="email"
                                class="form-control form-control-lg border-2 @error('email') is-invalid @enderror"
                                placeholder="Email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <div class="d-flex justify-content-between">
                                <label class="form-label small fw-semibold text-muted">Kata Sandi</label>
                                <a href="{{ route('password.request') }}" class="small text-decoration-none">Lupa
                                    Password?</a>
                            </div>
                            <input type="password" name="password"
                                class="form-control form-control-lg @error('password') is-invalid @enderror"
                                placeholder="Password" required>
                            @error('password')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="my-2 d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <label class="form-check-label text-muted">
                                    <input type="checkbox" name="remember" class="form-check-input" {{ old('remember') ? 'checked' : '' }}> Biarkan saya tetap masuk
                                </label>
                            </div>
                        </div>

                        <div class="mt-4 d-grid gap-2">
                            <button type="submit"
                                class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">MASUK</button>
                        </div>

                        <div class="position-relative my-4">
                            <hr class="text-muted">
                            <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">
                                Atau masuk dengan</span>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('auth.google') }}"
                                class="btn btn-outline-secondary btn-lg d-flex align-items-center justify-content-center py-2 shadow-sm border-2">
                                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google"
                                    width="18" class="me-2">
                                <span class="small fw-bold">Google</span>
                            </a>
                        </div>

                        <div class="text-center mt-4 pt-2">
                            <p class="text-muted mb-0">Belum punya akun?
                                <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none">Daftar
                                    Sekarang</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection