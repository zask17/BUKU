@extends('layouts.vendor.main')

@section('content')
<div class="row">
    <div class="col-md-4 stretch-card grid-margin">
        <div class="card bg-gradient-info text-white">
            <div class="card-body">
                <h4 class="font-weight-normal mb-3">Total Pendapatan <i class="mdi mdi-cash mdi-24px float-end"></i></h4>
                <h2 class="mb-5">Rp {{ number_format($stats['total_pendapatan'], 0, ',', '.') }}</h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 stretch-card grid-margin">
        <div class="card bg-gradient-success text-white">
            <div class="card-body">
                <h4 class="font-weight-normal mb-3">Pesanan Hari Ini <i class="mdi mdi-cart mdi-24px float-end"></i></h4>
                <h2 class="mb-5">{{ $stats['pesanan_hari_ini'] }} Pesanan</h2>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h4 class="card-title">Daftar Pesanan Masuk (Lunas)</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="bg-light text-center">
                    <tr>
                        <th>Waktu</th>
                        <th>Order ID</th>
                        <th>Nama Pelanggan</th>
                        <th>Menu Dipesan</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesananLunas as $idPesanan => $details)
                        @foreach($details as $index => $d)
                        <tr>
                            @if($index === 0)
                                <td rowspan="{{ $details->count() }}" class="align-middle text-center">
                                    {{ \Carbon\Carbon::parse($d->timestamp)->format('d M H:i') }}
                                </td>
                                <td rowspan="{{ $details->count() }}" class="align-middle font-weight-bold text-primary">
                                    {{ $d->pesanan->order_id_pg }}
                                </td>
                                <td rowspan="{{ $details->count() }}" class="align-middle">
                                    {{ $d->pesanan->nama }}
                                </td>
                            @endif
                            
                            <td>{{ $d->menu->nama_menu }}</td>
                            <td class="text-center">{{ $d->jumlah }}</td>
                            <td class="text-end">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada pesanan lunas untuk vendor Anda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection