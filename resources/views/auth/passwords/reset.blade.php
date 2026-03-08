@extends('layouts.guest.main-auth')

@section('title-page', 'Reset')

@section('content')
<div class="content-wrapper d-flex align-items-center auth">
    <div class="row flex-grow">
        <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left p-5 shadow-sm">
                <div class="brand-logo text-center">
                    <img src="{{ asset('assets/images/logo.svg') }}">
                </div>
                <h4 class="text-center">Buat Password Baru</h4>
                <h6 class="font-weight-light text-center">Pastikan password baru Anda kuat dan aman.</h6>
                
                <form class="pt-3" method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group mb-3">
                        <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" placeholder="Email" value="{{ $email ?? old('email') }}" required>
                        @error('email')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" id="password" placeholder="Password Baru" required autofocus>
                        @error('password')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <input type="password" name="password_confirmation" class="form-control form-control-lg" id="password-confirm" placeholder="Konfirmasi Password Baru" required>
                    </div>
                    <div class="mt-3 d-grid gap-2">
                        <button type="submit" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">RESET PASSWORD</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection