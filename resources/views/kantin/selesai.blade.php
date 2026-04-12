@extends('layouts.guest.main')

@section('title-page', 'Pembayaran Berhasil')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card text-center shadow-lg" style="border-top: 5px solid #28a745;">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <i class="mdi mdi-check-circle" style="font-size: 4rem; color: #28a745;"></i>
                    </div>
                    
                    <h2 class="card-title font-weight-bold mb-3">Pembayaran Berhasil!</h2>
                    
                    <p class="lead text-success mb-4">
                        <i class="mdi mdi-information-outline"></i>
                        Pesanan Anda telah diterima dan sedang diproses
                    </p>

                    <div class="alert alert-info" role="alert">
                        <h6 class="font-weight-bold">Detail Pesanan:</h6>
                        <small>
                            <p class="mb-1"><strong>Order ID:</strong> <br>
                                @if(session('order_id'))
                                    {{ session('order_id') }}
                                @else
                                    Cek email untuk detail pesanan
                                @endif
                            </p>
                            <p class="mb-0"><strong>Status:</strong> <br>
                                <span class="badge badge-success">✓ Lunas</span>
                            </p>
                        </small>
                    </div>

                    <hr>

                    <p class="text-muted mb-4">
                        Pesanan Anda akan segera disiapkan. Silakan tunggu pengumuman dari penjual kantin.
                    </p>

                    <div class="row">
                        <div class="col-6">
                            <a href="{{ route('kantin.index') }}" class="btn btn-outline-primary btn-block">
                                <i class="mdi mdi-arrow-left"></i> Pesan Lagi
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

            <div class="card mt-3" style="border-left: 4px solid #ffc107;">
                <div class="card-body">
                    <h6 class="font-weight-bold text-warning mb-2">
                        <i class="mdi mdi-clock-outline"></i> Waktu Penerimaan
                    </h6>
                    <p class="mb-0 text-muted small">
                        Pesanan biasanya siap dalam <strong>10-15 menit</strong>. 
                        Anda akan menerima notifikasi saat pesanan siap diambil.
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
