@extends('layouts.guest.main-auth')

@section('title-page', 'Register')

@section('content')
<div class="content-wrapper d-flex align-items-center auth">
    <div class="row flex-grow">
        <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left p-5 shadow-sm">
                <div class="brand-logo text-center">
                    <img src="{{ asset('assets/images/logo.svg') }}">
                </div>
                <h4 class="text-center">Baru di sini?</h4>
                <h6 class="font-weight-light text-center">Mendaftar itu mudah. Hanya butuh beberapa langkah.</h6>
                
                <form class="pt-3" id="registerForm" method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <input type="text" name="name" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name" placeholder="Nama Lengkap" value="{{ old('name') }}" required autocomplete="name" autofocus>
                        @error('name')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" placeholder="Email" value="{{ old('email') }}" required>
                        @error('email')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" id="password" placeholder="Password" required>
                        @error('password')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <input type="password" name="password_confirmation" class="form-control form-control-lg" id="password-confirm" placeholder="Konfirmasi Password" required>
                    </div>
                    <div class="mb-4">
                        <div class="form-check">
                            <label class="form-check-label text-muted">
                                <input type="checkbox" class="form-check-input" required> Saya setuju dengan semua Syarat & Ketentuan </label>
                        </div>
                    </div>
                    <div class="mt-3 d-grid gap-2">
                        <button type="submit" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">DAFTAR</button>
                    </div>
                    <div class="text-center mt-4 font-weight-light"> Sudah punya akun? <a href="{{ route('login') }}" class="text-primary">Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection