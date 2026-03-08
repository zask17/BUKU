@extends('layouts.guest.main-auth')

@section('title-page', 'Email')

@section('content')
<div class="content-wrapper d-flex align-items-center auth">
    <div class="row flex-grow">
        <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left p-5 shadow-sm">
                <div class="brand-logo text-center">
                    <img src="{{ asset('assets/images/logo.svg') }}">
                </div>
                <h4 class="text-center">Reset Password</h4>
                <h6 class="font-weight-light text-center">Masukkan email Anda untuk menerima link reset.</h6>

                @if (session('status'))
                    <div class="alert alert-success mt-3" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form class="pt-3" method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" placeholder="Alamat Email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="mt-3 d-grid gap-2">
                        <button type="submit" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">KIRIM LINK RESET</button>
                    </div>
                    <div class="text-center mt-4 font-weight-light">
                        <a href="{{ route('login') }}" class="text-primary">Kembali ke Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection