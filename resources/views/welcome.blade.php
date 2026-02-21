@extends('layouts.guest.main')

@section('style-page')
    <style>
        .landing-hero {
            text-align: center;
            padding: 60px 20px;
            background: #f8f9fa;
            border-radius: 15px;
            margin-bottom: 30px;
        }

        .landing-hero h1 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #343a40;
        }

        .cta-section {
            background: linear-gradient(to right, #19d895, #21d0f5);
            color: white;
            padding: 50px 20px;
            border-radius: 15px;
            text-align: center;
            margin-top: 30px;
        }

        .cta-section h2 {
            color: white;
        }

        .btn-outline-custom {
            border: 2px solid #19d895;
            color: #19d895;
            transition: 0.3s;
        }

        .btn-outline-custom:hover {
            background: #19d895;
            color: white;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="landing-hero shadow-sm">
            <div class="container">
                <h1 class="display-4">Halo Yang di Sana</h1>
                <p class="lead text-muted mt-3">
                    Jangan lupa makan karena gak semua punya someone buat diajak makan siang.
                </p>
            </div>
        </section>


        <div class="content-wrapper">
            <div class="page-header">
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="cta-section shadow-sm">
                        <h2>Ingin Menjadi Anggota?</h2>
                        <p class="mt-3">Daftar sekarang untuk akses penuh ke koleksi buku kami dan fitur menarik lainnya!
                        </p>
                        <a href="{{ route('register') }}" class="btn btn-gradient-primary btn-lg mt-3">Daftar Sekarang</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="cta-section shadow-sm">
                        <h2>Sudah Punya Akun?</h2>
                        <p class="mt-3">Masuk untuk melihat koleksi buku, kategori, dan informasi lainnya yang kami
                            sediakan.</p>
                        <a href="{{ route('login') }}" class="btn btn-gradient-primary btn-lg mt-3">Masuk Sekarang</a>
                    </div>
                </div>

            </div>
@endsection

        @section('js-page')
            <script>
                console.log("Welcome page loaded with Purple Admin layout.");
            </script>
        @endsection