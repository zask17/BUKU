@extends('layouts.vendor.main')

@section('title-page', 'Pesanan Management')

@section('content')
    <div id="notification-container"></div>

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Manajemen Pesanan Masuk</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">Order ID</th>
                                    <th width="15%">Nama Pelanggan</th>
                                    <th width="20%">Menu</th>
                                    <th width="5%">Qty</th>
                                    <th width="12%">Subtotal</th>
                                    <th width="12%">Status Bayar</th>
                                    <th width="12%">Metode</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $row)
                                    <tr>
                                        <td>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                                        <td><small class="font-weight-bold text-primary">{{ $row->pesanan->order_id_pg }}</small></td>
                                        <td>{{ $row->pesanan->nama }}</td>
                                        <td>{{ $row->menu->nama_menu }}</td>
                                        <td class="text-center">{{ $row->jumlah }}</td>
                                        <td class="font-weight-bold">Rp {{ number_format($row->subtotal, 0, ',', '.') }}</td>
                                        <td>
                                            @if ($row->pesanan->status_bayar == 0)
                                                <span class="badge badge-warning text-dark">Pending</span>
                                            @elseif ($row->pesanan->status_bayar == 1)
                                                <span class="badge badge-success">Paid</span>
                                            @elseif ($row->pesanan->status_bayar == 2)
                                                <span class="badge badge-danger">Failed</span>
                                            @else
                                                <span class="badge badge-secondary">Unknown</span>
                                            @endif
                                        </td>  
                                        <td>
                                            <small class="text-muted">
                                                {{ $row->pesanan->metode_bayar ?? 'Midtrans' }}
                                            </small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">Tidak ada data pesanan untuk vendor ini.</td>
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
        $(document).ready(function () {
            let notification = sessionStorage.getItem('notification');
            if (notification) {
                $('#notification-container').html(notification);
                sessionStorage.removeItem('notification');

                setTimeout(function () {
                    $('.alert').fadeOut('slow', function () {
                        $(this).remove();
                    });
                }, 5000);
            }
        });
    </script>
@endsection