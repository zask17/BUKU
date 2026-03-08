@extends('layouts.guest.main-auth')

@section('title-page', 'Verifikasi OTP')

@section('style-page')
<style>
    /* Styling khusus untuk input OTP agar terlihat modern */
    .otp-input {
        letter-spacing: 12px;
        font-weight: bold;
        font-size: 2rem !important;
        text-indent: 12px; /* Menyeimbangkan posisi teks agar tetap di tengah */
        border: 2px solid #ebedf2;
        border-radius: 8px;
        transition: border-color 0.3s;
    }
    .otp-input:focus {
        border-color: #b66dff;
        box-shadow: none;
    }
</style>
@endsection

@section('content')
<div class="content-wrapper d-flex align-items-center auth">
    <div class="row flex-grow">
        <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left p-5 shadow-sm">
                <div class="brand-logo text-center">
                    <img src="{{ asset('assets/images/logo.svg') }}" alt="logo">
                </div>
                <h4 class="text-center">Verifikasi OTP</h4>
                <h6 class="font-weight-light text-center">Masukkan 6 digit kode yang telah dikirim ke email Anda.</h6>

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        {{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form class="pt-3" method="POST" action="{{ route('otp.verify') }}">
                    @csrf
                    <div class="form-group mb-4 text-center">
                        <input type="text" 
                               name="otp" 
                               maxlength="6" 
                               class="form-control form-control-lg otp-input mx-auto" 
                               style="max-width: 250px;"
                               placeholder="000000"
                               required 
                               autofocus 
                               autocomplete="one-time-code">
                    </div>

                    <div class="mt-3 d-grid gap-2">
                        <button type="submit" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">
                            VERIFIKASI OTP
                        </button>
                    </div>

                    <div class="text-center mt-4 font-weight-light">
                        Tidak menerima kode? 
                        <form class="d-inline" method="POST" action="{{ route('otp.resend') }}">
                            @csrf
                            <button type="submit" class="btn btn-link p-0 text-primary fw-bold" style="text-decoration: none;">
                                Kirim Ulang
                            </button>
                        </form>
                    </div>

                    <div class="text-center mt-3">
                        <a href="{{ route('login') }}" class="text-muted small">Kembali ke Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection