@extends('layouts.visitor.main')

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
                <h1 class="display-4">halo pengunjung</h1>
                <p class="lead text-muted mt-3">
                    Jangan lupa makan karena gak semua punya someone buat diajak makan siang.
                </p>
                {{-- <div class="mt-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-gradient-primary btn-lg me-2">Buka Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-gradient-primary btn-lg me-2">Mulai Sekarang</a>
                    @endauth
                    <a href="#features" class="btn btn-outline-custom btn-lg">Lihat Fitur Database</a>
                </div> --}}
            </div>
        </section>

        {{-- <div class="row">
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-danger card-img-holder text-white">
                    <div class="card-body">
                        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                            alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Total Pengunjung <i
                                class="mdi mdi-account-multiple mdi-24px float-end"></i></h4>
                        <h2 class="mb-5">{{ $jumlahPengunjung }}</h2>
                        <h6 class="card-text">Terdaftar dalam sistem</h6>
                    </div>
                </div>
            </div> --}}

            <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-info card-img-holder text-white">
                    <div class="card-body">
                        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                            alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Total Kategori <i
                                class="mdi mdi-format-list-bulleted mdi-24px float-end"></i></h4>
                        <h2 class="mb-5">{{ $jumlahKategori }}</h2>
                        <h6 class="card-text">Novel, Biografi, Komik, dll.</h6>
                    </div>
                </div>
            </div>

            <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-success card-img-holder text-white">
                    <div class="card-body">
                        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                            alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Total Buku <i
                                class="mdi mdi-book-open-variant mdi-24px float-end"></i></h4>
                        <h2 class="mb-5">{{ $jumlahBuku }}</h2>
                        <h6 class="card-text">Koleksi buku tersedia</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="cta-section shadow">
        <div class="container text-center">
            <i class="mdi mdi-checkbox-marked-circle-outline display-3 mb-3 d-block"></i>
            <h2>Siap untuk meningkatkan efisiensi logistik Anda?</h2>
            <p class="mb-4">Kelola data dengan akurat, mulai dari pengadaan hingga penjualan akhir.</p>
            @guest
                <a href="{{ route('login') }}" class="btn btn-light btn-lg text-success font-weight-bold">
                    Masuk ke Sistem Sekarang
                </a>
            @endguest
        </div>
    </section>
    </div>
@endsection

@section('js-page')
    <script>
        console.log("Welcome page loaded with Purple Admin layout.");
    </script>
@endsection