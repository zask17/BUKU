@extends($layout)

@section('title-page', 'Pembayaran Berhasil')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card text-center shadow-lg" style="border-top: 5px solid #28a745;">
                    <div class="card-body py-5">
                        <div class="mb-4">
                            <i class="mdi mdi-check-circle" style="font-size: 4.5rem; color: #28a745;"></i>
                        </div>

                        <h2 class="card-title font-weight-bold mb-3">Pembayaran Berhasil!</h2>
                        <p class="lead">Terima kasih, <strong>{{ $pesanan->nama }}</strong></p>

                        <div class="mt-4 p-3 bg-light rounded">
                            <p class="font-weight-bold mb-2">Scan QR untuk Validasi di Vendor:</p>
                            <div class="bg-white p-3 d-inline-block shadow-sm rounded">
                                {!! $qrcode !!}
                            </div>
                            <p class="mt-2 text-muted">ID Pesanan: <strong>#{{ $pesanan->idpesanan }}</strong></p>
                        </div>

                        <hr>

                        <h5 class="text-start mb-3">Detail Pesanan Anda:</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Vendor</th>
                                        <th>Menu</th>
                                        <th>Jumlah</th>
                                        <th>Harga</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pesanan->details as $detail)
                                    <tr>
                                        <td>
                                            <strong>
                                                {{ $detail->menu->vendor->nama_vendor ?? 'Umum' }}
                                            </strong>
                                        </td>
                                        <td>{{ $detail->menu->nama_menu }}</td>
                                        <td class="text-center">{{ $detail->jumlah }}</td>
                                        <td class="text-end">Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-info mt-4">
                            <strong>Total Bayar: Rp {{ number_format($pesanan->total, 0, ',', '.') }}</strong><br>
                            Status: <span class="badge badge-success">Lunas</span>
                        </div>

                        <p class="text-muted small mt-3">
                            Tunjukkan QR Code ini kepada vendor terkait saat mengambil pesanan.
                        </p>

                        <div class="row mt-4">
                            <div class="col-6">
                                <a href="{{ route('kantin.index') }}" class="btn btn-outline-primary btn-block">
                                    <i class="mdi mdi-arrow-left"></i> Pesan Lagi
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('welcome') }}" class="btn btn-primary btn-block">
                                    <i class="mdi mdi-home"></i> Kembali ke Beranda
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection