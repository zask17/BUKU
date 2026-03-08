@extends('layouts.guest.main-auth')

@section('title-page', 'Confirm')

@section('content')
<div class="content-wrapper d-flex align-items-center auth">
    <div class="row flex-grow">
        <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left p-5 shadow-sm">
                <div class="brand-logo text-center">
                    <img src="{{ asset('assets/images/logo.svg') }}">
                </div>
                <h4 class="text-center">Konfirmasi Password</h4>
                <p class="text-muted text-center">Harap konfirmasi password Anda sebelum melanjutkan.</p>

                <form class="pt-3" method="POST" action="{{ route('password.confirm') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" name="password" placeholder="Password" required autocomplete="current-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="mt-3 d-grid gap-2">
                        <button type="submit" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">KONFIRMASI PASSWORD</button>
                    </div>

                    @if (Route::has('password.request'))
                        <div class="text-center mt-3">
                            <a class="text-primary" href="{{ route('password.request') }}">
                                {{ __('Lupa Password?') }}
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection