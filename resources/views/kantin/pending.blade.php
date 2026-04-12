@extends('layouts.guest.main')

@section('title-page', 'Menunggu Pembayaran')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card text-center shadow-lg" style="border-top: 5px solid #ffc107;">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <i class="mdi mdi-clock-outline" style="font-size: 4rem; color: #ffc107;"></i>
                    </div>
                    
                    <h2 class="card-title font-weight-bold mb-3">Pembayaran Tertunda</h2>
                    
                    <p class="lead text-warning mb-4">
                        <i class="mdi mdi-alert-outline"></i>
                        Pesanan Anda belum dikonfirmasi
                    </p>

                    <div class="alert alert-warning" role="alert">
                        <h6 class="font-weight-bold">Status Pembayaran:</h6>
                        <small>
                            <p class="mb-1"><strong>Kondisi:</strong> <br>
                                Pembayaran masih dalam proses atau belum diterima
                            </p>
                            <p class="mb-0"><strong>Tindakan:</strong> <br>
                                Silakan selesaikan pembayaran atau coba lagi
                            </p>
                        </small>
                    </div>

                    <hr>

                    <p class="text-muted mb-4">
                        Jika pembayaran telah diselesaikan, mohon tunggu beberapa saat karena sistem memproses konfirmasi dari bank/payment gateway.
                    </p>

                    <div class="mb-3">
                        <small class="text-danger">
                            <i class="mdi mdi-information-outline"></i>
                            Pesanan akan otomatis dibatalkan dalam <strong>1 jam</strong> jika pembayaran tidak dikonfirmasi.
                        </small>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <a href="{{ route('kantin.index') }}" class="btn btn-outline-primary btn-block">
                                <i class="mdi mdi-arrow-left"></i> Kembali
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

            <div class="card mt-3" style="border-left: 4px solid #17a2b8;">
                <div class="card-body">
                    <h6 class="font-weight-bold text-info mb-2">
                        <i class="mdi mdi-help-circle-outline"></i> Bantuan
                    </h6>
                    <p class="mb-0 text-muted small">
                        Jika ada kendala dengan pembayaran, silakan hubungi administrator atau coba ulangi transaksi.
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
