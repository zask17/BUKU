@extends($layout)

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

                        <p>Terima kasih <strong>{{ $pesanan->nama }}</strong></p>
                        
                        <div class="mt-4 p-3 bg-light d-inline-block" style="border-radius: 15px;">
                            <p class="font-weight-bold mb-2">Scan QR untuk Validasi:</p>
                            <div class="bg-white p-2 d-inline-block shadow-sm">
                                {!! $qrcode !!}
                            </div>
                            <p class="mt-2 text-muted mb-0">ID Pesanan: #{{ $pesanan->idpesanan }}</p>
                        </div>

                        <p class="lead text-success mt-4 mb-4">
                            <i class="mdi mdi-information-outline"></i>
                            Pesanan Anda telah diterima dan sedang diproses
                        </p>

                        <div class="alert alert-info" role="alert">
                            <h6 class="font-weight-bold">Detail Pesanan:</h6>
                            <small>
                                <p class="mb-1"><strong>Order ID Midtrans:</strong> <br>
                                    {{ $pesanan->order_id_pg ?? 'N/A' }}
                                </p>
                                <p class="mb-0"><strong>Status:</strong> <br>
                                    <span class="badge badge-success">✓ Lunas / Paid</span>
                                </p>
                            </small>
                        </div>

                        <hr>

                        <p class="text-muted mb-4 small">
                            Tunjukkan QR Code di atas kepada vendor/penjual saat mengambil makanan.
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
            width: 100%;
        }
        svg {
            max-width: 150px;
            height: auto;
        }
    </style>
@endsection