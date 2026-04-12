@extends('layouts.guest.main')

@section('title-page', 'Pembayaran Gagal')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card text-center shadow-lg" style="border-top: 5px solid #dc3545;">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <i class="mdi mdi-close-circle" style="font-size: 4rem; color: #dc3545;"></i>
                    </div>
                    
                    <h2 class="card-title font-weight-bold mb-3">Pembayaran Gagal</h2>
                    
                    <p class="lead text-danger mb-4">
                        <i class="mdi mdi-alert-outline"></i>
                        Transaksi Anda tidak dapat diproses
                    </p>

                    <div class="alert alert-danger" role="alert">
                        <h6 class="font-weight-bold">Sebab Kemungkinan:</h6>
                        <small>
                            <ul class="text-left mb-0">
                                <li>Pembayaran ditolak oleh bank</li>
                                <li>Saldo tidak mencukupi</li>
                                <li>Waktu pembayaran telah expired</li>
                                <li>Kesalahan data pembayaran</li>
                            </ul>
                        </small>
                    </div>

                    <hr>

                    <p class="text-muted mb-4">
                        Pesanan Anda telah dibatalkan. Silakan coba kembali dengan metode pembayaran lain atau pastikan data pembayaran Anda benar.
                    </p>

                    <div class="row">
                        <div class="col-6">
                            <a href="{{ route('kantin.index') }}" class="btn btn-outline-primary btn-block">
                                <i class="mdi mdi-reload"></i> Coba Lagi
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('welcome') }}" class="btn btn-gradient-primary btn-block">
                                <i class="mdi mdi-home"></i> Beranda
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3" style="border-left: 4px solid #6c757d;">
                <div class="card-body">
                    <h6 class="font-weight-bold text-secondary mb-2">
                        <i class="mdi mdi-information-outline"></i> Catatan
                    </h6>
                    <p class="mb-0 text-muted small">
                        Data pesanan Anda yang belum dibayar akan dihapus secara otomatis dalam sistem. 
                        Anda dapat membuat pesanan baru kapan saja.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 10px;
    }
    
    .btn-block {
        border-radius: 6px;
    }
</style>
@endsection
