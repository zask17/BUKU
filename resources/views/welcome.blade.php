@extends('layouts.guest.main')

@section('title-page', 'Dashboard')

{{-- @section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">Dashboard</li>
@endsection --}}

@section('style-page')
    <style>
        .stat-card-link {
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
        }

        .stat-card-link:hover {
            transform: translateY(-8px);
        }

        .stat-card-link:hover .card {
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2) !important;
            transform: scale(1.02);
        }
    </style>
@endsection

@section('content')
    {{-- Hero Greeting --}}
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card"
                style="background: linear-gradient(to right, #da8ee7, #a283f2); border: none; border-radius: 12px;">
                <div class="card-body text-white text-center py-4">
                    <h2 class="font-weight-bold mb-2">Halo! 👋</h2>
                    <p class="mb-0" style="color: rgba(255,255,255,0.85); font-size: 1rem;">
                        Mari jelajahi ilmu pengetahuan hari ini.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik Utama --}}
    <div class="row">
        <div class="col-md-4 stretch-card grid-margin">
            <a href="{{ route('buku') }}" class="stat-card-link">
                <div class="card bg-gradient-danger card-img-holder text-white"
                    style="transition: all 0.3s ease; cursor: pointer;">
                    <div class="card-body">
                        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                            alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Total Koleksi Buku <i
                                class="mdi mdi-book-open-page-variant mdi-24px float-end"></i></h4>
                        <h2 class="mb-5">{{ $totalBuku ?? 0 }} Buku</h2>
                        <h6 class="card-text">Terus bertambah setiap harinya</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 stretch-card grid-margin">
            <a href="{{ route('kategori') }}" class="stat-card-link">
                <div class="card bg-gradient-info card-img-holder text-white"
                    style="transition: all 0.3s ease; cursor: pointer;">
                    <div class="card-body">
                        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                            alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Kategori Tersedia <i
                                class="mdi mdi-bookmark-outline mdi-24px float-end"></i></h4>
                        <h2 class="mb-5">{{ $totalKategori ?? 0 }} Kategori</h2>
                        <h6 class="card-text">Pilihan bacaan yang beragam</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 stretch-card grid-margin">
            <a href="{{ route('wilayah.index_ajax') }}" class="stat-card-link">
                <div class="card bg-gradient-success card-img-holder text-white"
                    style="transition: all 0.3s ease; cursor: pointer;">
                    <div class="card-body">
                        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                            alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Data Wilayah <i
                                class="mdi mdi-map-marker-radius mdi-24px float-end"></i></h4>
                        <h2 class="mb-5">{{ $totalWilayah ?? 0 }} Wilayah</h2>
                        <h6 class="card-text">Provinsi, Kota, Kecamatan, Kelurahan</h6>
                    </div>
                </div>
            </a>
        </div>
        <div>
            <a href="{{ route('wilayah.index_axios') }}" class="stat-card-link">
                <div class="card bg-gradient-warning card-img-holder text-white"
                    style="transition: all 0.3s ease; cursor: pointer;">
                    <div class="card-body">
                        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                            alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Data Wilayah (Axios) <i
                                class="mdi mdi-map-marker-radius mdi-24px float-end"></i></h4>
                        <h2 class="mb-5">{{ $totalWilayah ?? 0 }} Wilayah</h2>
                        <h6 class="card-text">Provinsi, Kota, Kecamatan, Kelurahan</h6>
                    </div>
                </div>
            </a>
        </div>
        {{-- <div class="col-md-4 stretch-card grid-margin">
            <a href="{{ route('kategori') }}" class="stat-card-link">
                <div class="card bg-gradient-success card-img-holder text-white"
                    style="transition: all 0.3s ease; cursor: pointer;">
                    <div class="card-body">
                        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                            alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Total Genre <i
                                class="mdi mdi-palette-outline mdi-24px float-end"></i></h4>
                        <h2 class="mb-5">{{ count($kategoriStats) ?? 0 }}</h2>
                        <h6 class="card-text">Genre menarik menanti Anda</h6>
                    </div>
                </div>
            </a>
        </div> --}}
    </div>

    {{-- Koleksi Buku Terbaru (Grid Layout) --}}
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="card-title mb-0">Koleksi Buku Terbaru</h4>
                <a href="{{ route('buku') }}" class="text-primary small">Lihat Semua <i class="mdi mdi-arrow-right"></i></a>
            </div>
            <div class="row">
                @forelse($bukuTerbaru ?? [] as $buku)
                    <div class="col-md-3 col-sm-6 grid-margin stretch-card">
                        <div class="card shadow-sm border-0">
                            <div class="card-body p-3">
                                <div class="text-center bg-light rounded py-4 mb-3">
                                    <i class="mdi mdi-book-variant text-primary" style="font-size: 50px;"></i>
                                </div>
                                <h5 class="text-truncate font-weight-bold mb-1">{{ $buku->judul }}</h5>
                                <p class="text-muted small mb-2">{{ $buku->pengarang ?? 'Penulis Tidak Diketahui' }}</p>
                                <p class="text-muted small mb-3">
                                    <i class="mdi mdi-label"></i> {{ $buku->kategori->nama_kategori ?? 'Tanpa Kategori' }}
                                </p>
                                <div class="d-grid">
                                    <a href="{{ route('buku') }}" class="btn btn-outline-primary btn-sm">Lihat Detail</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- Dummy Data jika database kosong agar tampilan tidak jelek --}}
                    @for($i = 1; $i <= 4; $i++)
                        <div class="col-md-3 col-sm-6 grid-margin stretch-card">
                            <div class="card shadow-sm border-0">
                                <div class="card-body p-3 text-center">
                                    <div class="bg-light rounded py-4 mb-3 text-muted">
                                        <i class="mdi mdi-book-image" style="font-size: 50px;"></i>
                                    </div>
                                    <h5 class="font-weight-bold">Buku Contoh #{{ $i }}</h5>
                                    <p class="text-muted small mb-3">Kategori Umum</p>
                                    <div class="d-grid"><button class="btn btn-outline-primary btn-sm" disabled>Detail Buku</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endfor
                @endforelse
            </div>
        </div>
    </div>

    {{-- Grafik Statistik --}}
    <div class="row">
        <div class="col-md-7 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Pertumbuhan Koleksi per Kategori</h4>
                    <canvas id="pertumbuhan-chart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-5 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Distribusi Kategori</h4>
                    <canvas id="traffic-chart" style="max-height: 300px;"></canvas>
                    <div id="traffic-chart-legend" class="rounded-legend legend-vertical legend-bottom-left pt-4"></div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js-page')
    <script src="{{ asset('assets/vendors/chart.js/chart.umd.js') }}"></script>
    <script>
        // Setup data chart untuk Distribusi Kategori (Doughnut)
        const kategoriLabels = {!! json_encode($kategoriStats->pluck('nama') ?? ['Teknologi', 'Fiksi', 'Sains']) !!};
        const kategoriData = {!! json_encode($kategoriStats->pluck('total') ?? [40, 30, 30]) !!};

        // Setup data chart untuk Pertumbuhan Koleksi (Bar/Line)
        const pertumbuhanLabels = {!! json_encode($pertumbuhanData->pluck('nama') ?? []) !!};
        const pertumbuhanValues = {!! json_encode($pertumbuhanData->pluck('total') ?? []) !!};

        // Grafik Bar - Pertumbuhan Koleksi per Kategori
        if ($("#pertumbuhan-chart").length) {
            const ptCtx = document.getElementById('pertumbuhan-chart');
            new Chart(ptCtx, {
                type: 'bar',
                data: {
                    labels: pertumbuhanLabels,
                    datasets: [{
                        label: 'Jumlah Buku',
                        data: pertumbuhanValues,
                        backgroundColor: '#b66dff',
                        borderColor: '#9d4edd',
                        borderWidth: 2,
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: true, position: 'top' }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: Math.max(...pertumbuhanValues) + 5
                        }
                    }
                }
            });
        }

        // Grafik Doughnut - Distribusi Kategori
        if ($("#traffic-chart").length) {
            const ctx = document.getElementById('traffic-chart');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: kategoriData,
                        backgroundColor: ['#b66dff', '#ffab2d', '#1bcfb4', '#ff6b6b', '#4d96ff'],
                    }],
                    labels: kategoriLabels
                },
                options: {
                    cutout: '70%',
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }
    </script>
@endsection