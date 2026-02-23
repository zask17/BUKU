@extends('layouts.guest.main')


@section('title-page', 'otp verify')

{{-- Nonadkitfkan navbar dan sidebar --}}
@php
    $hideNavbar = true;
    $hideSidebar = true;
@endphp

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Verifikasi Kode OTP</h4>
                </div>
                <div class="card-body">
                    <p class="text-center">Masukkan 6 digit kode OTP yang dikirim ke email Anda</p>

                    @if ($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="{{ route('otp.verify') }}">
                        @csrf
                        <div class="d-flex justify-content-center mb-4">
                            <input type="text" name="otp" maxlength="6" class="form-control text-center fs-3" 
                                   style="width: 180px; letter-spacing: 10px;" required autofocus>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Verifikasi OTP</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection