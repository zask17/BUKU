@extends('layouts.guest.main-auth')

@section('title-page', 'Login')

@section('content')
<div class="content-wrapper d-flex align-items-center auth">
    <div class="row flex-grow">
        <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left p-5 shadow-sm">
                <div class="brand-logo text-center">
                    <img src="{{ asset('assets/images/logo.svg') }}" alt="logo">
                </div>
                <h4 class="text-center">Halo! Mari kita mulai</h4>
                <h6 class="font-weight-light text-center">Masuk untuk melanjutkan.</h6>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form class="pt-3" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-3">
                        <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" placeholder="Password" required>
                        @error('password')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="mt-3 d-grid gap-2">
                        <button type="submit" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">MASUK</button>
                    </div>

                    <div class="my-2 d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <label class="form-check-label text-muted">
                                <input type="checkbox" name="remember" class="form-check-input" {{ old('remember') ? 'checked' : '' }}> Biarkan saya tetap masuk 
                            </label>
                        </div>
                        <a href="{{ route('password.request') }}" class="auth-link text-primary">Lupa password?</a>
                    </div>

                    <div class="mb-2 d-grid gap-2">
                        <a href="{{ route('auth.google') }}" class="btn btn-block btn-outline-danger btn-lg auth-form-btn d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-google me-2"></i> Masuk dengan Google 
                        </a>
                    </div>

                    <div class="text-center mt-4 font-weight-light"> 
                        Belum punya akun? <a href="{{ route('register') }}" class="text-primary">Daftar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection