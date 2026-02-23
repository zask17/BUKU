@extends('layouts.guest.main')

@section('title-page', 'Login')

{{-- Nonadkitfkan navbar dan sidebar --}}
@php
    $hideNavbar = true;
    $hideSidebar = true;
@endphp

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0 mt-5">

                    <div class="card-header bg-gradient-primary text-white text-center py-4">
                        <h3 class="mb-0">Masuk ke Akun Anda</h3>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-4">
                                <label for="email" class="form-label fw-bold">Email</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') }}" required autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label fw-bold">Password</label>
                                <input id="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-gradient-primary btn-lg">
                                    Masuk
                                </button>
                            </div>

                            <div class="d-grid mt-3">
                                <a href="{{ route('auth.google') }}"
                                    class="btn btn-outline-danger btn-lg d-flex align-items-center justify-content-center">
                                    <img src="https://tse2.mm.bing.net/th/id/OIP.JD8-Cre3QL5J4IVKeUKwpAHaHa?rs=1&pid=ImgDetMain&o=7&rm=3"
                                        alt="Google" style="height: 20px; margin-right: 10px;">
                                    Masuk dengan Google
                                </a>
                            </div>

                            {{-- <div class="text-center mt-3">
                                <hr>
                                <p class="text-muted small">Atau masuk menggunakan akun email</p>
                            </div> --}}

                            <div class="text-center mt-4">
                                <a href="{{ route('password.request') }}" class="text-muted">Lupa password?</a>
                            </div>
                        </form>
                    </div>

                    <div class="card-footer bg-light text-center py-3">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="text-primary fw-bold">Daftar di sini</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection