@extends('layouts.admin.main')

@section('title-page')
    <span class="page-title-icon bg-gradient-primary text-white me-2">
        <i class="mdi mdi-home"></i>
    </span> Dashboard
@endsection

@section('style-page')
    <script src="{{ asset('assets/js/dashboard.js') }}" defer></script>
@endsection

@section('content')

    {{-- Hero Greeting --}}
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card" style="background: linear-gradient(to right, #19d895, #21d0f5); border: none; border-radius: 12px;">
                <div class="card-body text-white text-center py-4">
                    <h2 class="font-weight-bold mb-2" style="color: white;">Halo Admin 👋</h2>
                    <p class="mb-0" style="color: rgba(255,255,255,0.85); font-size: 1rem;">
                        Jangan lupa makan karena gak semua punya someone buat diajak makan siang.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="row">
        <div class="col-md-4 stretch-card grid-margin">
            <div class="card bg-gradient-danger card-img-holder text-white">
                <div class="card-body">
                    <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">
                        Total Pengguna
                        <i class="mdi mdi-account-multiple mdi-24px float-end"></i>
                    </h4>
                    <h2 class="mb-5">{{ $jumlahPengguna }}</h2>
                    <h6 class="card-text">Terdaftar dalam sistem</h6>
                </div>
            </div>
        </div>

        <div class="col-md-4 stretch-card grid-margin">
            <div class="card bg-gradient-info card-img-holder text-white">
                <div class="card-body">
                    <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">
                        Total Kategori
                        <i class="mdi mdi-format-list-bulleted mdi-24px float-end"></i>
                    </h4>
                    <h2 class="mb-5">{{ $jumlahKategori }}</h2>
                    <h6 class="card-text">Novel, Biografi, Komik, dll.</h6>
                </div>
            </div>
        </div>

        <div class="col-md-4 stretch-card grid-margin">
            <div class="card bg-gradient-success card-img-holder text-white">
                <div class="card-body">
                    <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">
                        Total Buku
                        <i class="mdi mdi-book-open-variant mdi-24px float-end"></i>
                    </h4>
                    <h2 class="mb-5">{{ $jumlahBuku }}</h2>
                    <h6 class="card-text">Koleksi buku yang tersedia</h6>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="row">
        <div class="col-md-7 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="clearfix">
                        <h4 class="card-title float-start">Statistik Kunjungan & Peminjaman</h4>
                        <div id="visit-sale-chart-legend" class="rounded-legend legend-horizontal legend-top-right float-end"></div>
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
                    <h4 class="card-title">Aktivitas Terbaru</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Pengguna</th>
                                    <th>Aktivitas</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($aktivitasTerbaru ?? [] as $aktivitas)
                                    <tr>
                                        <td>
                                            <img src="{{ asset('assets/images/faces/face1.jpg') }}" class="me-2" alt="image"
                                                style="width:30px; height:30px; border-radius:50%; object-fit:cover;">
                                            {{ $aktivitas->nama_pengguna ?? '-' }}
                                        </td>
                                        <td>{{ $aktivitas->deskripsi ?? '-' }}</td>
                                        <td>
                                            <label class="badge badge-gradient-success">Selesai</label>
                                        </td>
                                        <td>{{ $aktivitas->created_at?->format('d M Y') ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td>
                                            <img src="{{ asset('assets/images/faces/face1.jpg') }}" class="me-2" alt="image"
                                                style="width:30px; height:30px; border-radius:50%; object-fit:cover;">
                                            Admin
                                        </td>
                                        <td>Menambahkan buku baru ke koleksi</td>
                                        <td><label class="badge badge-gradient-success">SELESAI</label></td>
                                        <td>{{ now()->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <img src="{{ asset('assets/images/faces/face2.jpg') }}" class="me-2" alt="image"
                                                style="width:30px; height:30px; border-radius:50%; object-fit:cover;">
                                            Pengguna Baru
                                        </td>
                                        <td>Mendaftar sebagai anggota</td>
                                        <td><label class="badge badge-gradient-info">BARU</label></td>
                                        <td>{{ now()->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <img src="{{ asset('assets/images/faces/face3.jpg') }}" class="me-2" alt="image"
                                                style="width:30px; height:30px; border-radius:50%; object-fit:cover;">
                                            Pembaca
                                        </td>
                                        <td>Meminjam buku kategori Novel</td>
                                        <td><label class="badge badge-gradient-warning">DIPINJAM</label></td>
                                        <td>{{ now()->format('d M Y') }}</td>
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