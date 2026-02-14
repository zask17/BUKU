@extends('layouts.admin.main')

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
            <h1 class="display-4">Sistem Manajemen Inventaris & Pengadaan Toko</h1>
            <p class="lead text-muted mt-3">
                Kelola stok barang, pengadaan, penerimaan, dan penjualan dengan mudah dan efisien. <br>
                Ditenagai oleh basis data relasional yang kokoh dengan otomatisasi tingkat tinggi.
            </p>
            <div class="mt-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-gradient-primary btn-lg me-2">Buka Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-gradient-primary btn-lg me-2">Mulai Sekarang</a>
                @endauth
                <a href="#features" class="btn btn-outline-custom btn-lg">Lihat Fitur Database</a>
            </div>
        </div>
    </section>

    <div class="row" id="features">
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-success"><i class="mdi mdi-settings me-2"></i>Stored Procedure</h4>
                    <p>Automatisasi kompleks: Proses penerimaan barang dan finalisasi nilai total pengadaan otomatis.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-success"><i class="mdi mdi-sync me-2"></i>Database Trigger</h4>
                    <p>Pencatatan Kartu Stok (mutasi stok) otomatis setelah proses Penerimaan dan Penjualan barang.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-success"><i class="mdi mdi-trending-up me-2"></i>Database Function</h4>
                    <p>Perhitungan harga jual secara otomatis termasuk margin dan pengecekan stok secara real-time.</p>
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