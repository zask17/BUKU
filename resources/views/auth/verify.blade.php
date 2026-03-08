@extends('layouts.guest.main-auth')

@section('title-page', 'Verifikasi')

@section('content')
<div class="content-wrapper d-flex align-items-center auth">
    <div class="row flex-grow">
        <div class="col-lg-5 mx-auto">
            <div class="auth-form-light text-left p-5 shadow-sm">
                <div class="brand-logo text-center">
                    <img src="{{ asset('assets/images/logo.svg') }}">
                </div>
                <h4 class="text-center">Verifikasi Email Anda</h4>
                
                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('Link verifikasi baru telah dikirim ke alamat email Anda.') }}
                        </div>
                    @endif

                    <p class="text-muted">Sebelum melanjutkan, harap periksa email Anda untuk link verifikasi.</p>
                    <p class="text-muted">Jika Anda tidak menerima email tersebut,</p>
                    
                    <form class="d-grid gap-2" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-gradient-primary btn-sm">{{ __('Klik di sini untuk kirim ulang') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection