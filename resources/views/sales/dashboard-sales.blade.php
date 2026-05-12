@extends('layouts.admin.main')

@section('title-page', 'Kunjungan Toko')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Kunjungan Sales</li>
@endsection

@section('style-page')
<style>
    #reader { width: 100%; border: none !important; }
    .scan-box { border: 2px dashed #b66dff; padding: 10px; border-radius: 10px; }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-5 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Scanner Barcode</h4>
                <div class="scan-box">
                    <div id="reader"></div>
                </div>
                <div id="scan-loading" class="d-none text-center mt-3">
                    <div class="spinner-border text-primary"></div>
                    <p id="scan-loading-text" class="small mt-2">Memproses...</p>
                </div>
                <div id="notification-container" class="mt-3"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-7 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Hasil Validasi</h4>
                <div id="scan-result-content">
                    <p class="text-muted text-center py-5">Silahkan scan barcode toko untuk validasi lokasi.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Riwayat Kunjungan</h4>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Toko</th>
                                <th>Jarak</th>
                                <th>Status</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody id="riwayat-tbody">
                            @foreach($riwayat as $r)
                            <tr>
                                <td>{{ $r->toko->nama_toko }}</td>
                                <td>{{ $r->jarak }}m</td>
                                <td><span class="badge {{ $r->status == 'diterima' ? 'badge-success' : 'badge-danger' }}">{{ $r->status }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($r->waktu)->format('H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-page')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    window.barcodeScannerConfig = {
        lookupUrl: "{{ url('sales/barcode') }}",
        beepUrl: "https://www.soundjay.com/buttons/beep-07.mp3"
    };
    window.salesConfig = {
        storeSalesUrl: "{{ route('sales.store') }}",
        csrfToken: "{{ csrf_token() }}"
    };
</script>
<script src="{{ asset('assets/js/sales.js') }}"></script>
@endsection