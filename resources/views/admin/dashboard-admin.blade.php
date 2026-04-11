@extends('layouts.admin.main')

@section('title-page')
    <span class="page-title-icon bg-gradient-primary text-white me-2">
        <i class="mdi mdi-home"></i>
    </span> Dashboard
@endsection

@section('style-page')
    <script src="{{ asset('assets/js/dashboard.js') }}" defer></script>
    <style>
        .stat-card-link {
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .stat-card-link:hover {
            transform: translateY(-8px);
        }

        .stat-card-link:hover .card {
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2) !important;
            transform: scale(1.02);
        }

        .stat-card-link .card {
            flex: 1;
        }
    </style>
@endsection

@section('content')

    {{-- Hero Greeting --}}
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card"
                style="background: linear-gradient(to right, #19d895, #21d0f5); border: none; border-radius: 12px;">
                <div class="card-body text-white text-center py-4">
                    <h2 class="font-weight-bold mb-2" style="color: white;">Halo Admin 👋</h2>
                    <p class="mb-0" style="color: rgba(255,255,255,0.85); font-size: 1rem;">
                        Jangan lupa makan karena gak semua punya someone buat diajak makan siang.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik Utama - Baris 1 --}}
    <div class="row">
        <div class="col-md-4 stretch-card grid-margin">
            <a href="{{ route('admin.pengguna') }}" class="stat-card-link">
                <div class="card bg-gradient-danger card-img-holder text-white border-0">
                    <div class="card-body">
                        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                            alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">
                            Total Pengguna
                            <i class="mdi mdi-account-multiple mdi-24px float-end"></i>
                        </h4>
                        <h2 class="mb-5">{{ $jumlahPengguna }}</h2>
                        <h6 class="card-text">Terdaftar dalam sistem</h6>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4 stretch-card grid-margin">
            <a href="{{ route('admin.pengguna') }}" class="stat-card-link">
                <div class="card bg-gradient-info card-img-holder text-white border-0">
                    <div class="card-body">
                        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                            alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">
                            Admin
                            <i class="mdi mdi-account-tie mdi-24px float-end"></i>
                        </h4>
                        <h2 class="mb-5">{{ $jumlahAdmin }}</h2>
                        <h6 class="card-text">Pengguna Admin</h6>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4 stretch-card grid-margin">
            <a href="{{ route('admin.pengguna') }}" class="stat-card-link">
                <div class="card bg-gradient-warning card-img-holder text-white border-0">
                    <div class="card-body">
                        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                            alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">
                            Pengunjung
                            <i class="mdi mdi-account mdi-24px float-end"></i>
                        </h4>
                        <h2 class="mb-5">{{ $jumlahVisitor }}</h2>
                        <h6 class="card-text">User Pengunjung</h6>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Statistik Utama - Baris 2 --}}
    <div class="row">
        <div class="col-md-4 stretch-card grid-margin">
            <a href="{{ route('admin.kategori.index') }}" class="stat-card-link">
                <div class="card bg-gradient-primary card-img-holder text-white border-0">
                    <div class="card-body">
                        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                            alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">
                            Total Kategori
                            <i class="mdi mdi-format-list-bulleted mdi-24px float-end"></i>
                        </h4>
                        <h2 class="mb-5">{{ $jumlahKategori }}</h2>
                        <h6 class="card-text">Kategori tersedia</h6>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4 stretch-card grid-margin">
            <a href="{{ route('admin.buku.index') }}" class="stat-card-link">
                <div class="card bg-gradient-success card-img-holder text-white border-0">
                    <div class="card-body">
                        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                            alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">
                            Total Buku
                            <i class="mdi mdi-book-open-variant mdi-24px float-end"></i>
                        </h4>
                        <h2 class="mb-5">{{ $jumlahBuku }}</h2>
                        <h6 class="card-text">Koleksi buku tersedia</h6>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="row">
        <div class="col-md-7 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="clearfix">
                        <h4 class="card-title float-start">Statistik Kunjungan & Peminjaman</h4>
                        <div id="visit-sale-chart-legend"
                            class="rounded-legend legend-horizontal legend-top-right float-end"></div>
                    </div>
                    <canvas id="visit-sale-chart" class="mt-4"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-5 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Distribusi Kategori Buku</h4>
                    <div class="doughnutjs-wrapper d-flex justify-content-center">
                        <canvas id="traffic-chart"></canvas>
                    </div>
                    <div id="traffic-chart-legend" class="rounded-legend legend-vertical legend-bottom-left pt-4"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity Table --}}
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Aktivitas Terbaru (User Baru)</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Pengguna</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Tanggal Bergabung</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($aktivitasTerbaru as $user)
                                    <tr>
                                        <td>
                                            <img src="{{ asset('assets/images/faces/face1.jpg') }}" class="me-2" alt="image">
                                            {{ $user->nama_user }}
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <label class="badge 
                                            @if($user->idrole == 1) badge-gradient-primary 
                                            @elseif($user->idrole == 2) badge-gradient-info 
                                            @elseif($user->idrole == 3) badge-gradient-success
                                            @else badge-gradient-secondary 
                                            @endif">
                                                @if($user->idrole == 1) ADMIN
                                                @elseif($user->idrole == 2) VISITOR
                                                @elseif($user->idrole == 3) VENDOR
                                                @else UNKNOWN
                                                @endif
                                            </label>
                                        </td>
                                        <td>
                                            {{-- Perbaikan Error: Cek apakah created_at tidak null sebelum format --}}
                                            {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada data aktivitas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js-page')
    <script>
        console.log("Dashboard admin loaded.");
    </script>
@endsection