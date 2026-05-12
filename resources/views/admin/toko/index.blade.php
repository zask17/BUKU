@extends('layouts.admin.main')

@section('title-page', 'Manajemen Toko')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Daftar Toko</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">Data Lokasi Toko</h4>
                    <a href="{{ route('admin.toko.create') }}" class="btn btn-gradient-primary">
                        <i class="mdi mdi-plus"></i> Tambah Toko
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Barcode</th>
                                <th>Nama Toko</th>
                                <th>Koordinat (Lat, Long)</th>
                                <th>Akurasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($toko as $item)
                            <tr>
                                <td>
                                    <img src="data:image/png;base64,{{ $item->barcode_base64 }}" alt="barcode" style="width: 100px; height: auto; border-radius: 0;">
                                    <br><small class="text-muted">{{ $item->idtoko }}</small>
                                </td>
                                <td>{{ $item->nama_toko }}</td>
                                <td><small>{{ $item->latitude }}, {{ $item->longtitude }}</small></td>
                                <td>{{ $item->accuracy }}m</td>
                                <td>
                                    <a href="{{ route('admin.toko.edit', $item->idtoko) }}" class="btn btn-sm btn-info">Edit</a>
                                    <button class="btn btn-sm btn-danger btn-delete" data-id="{{ $item->idtoko }}">Hapus</button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center">Data kosong</td></tr>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Toko?',
            text: "Data ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('admin/toko/delete') }}/" + id,
                    type: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(res) {
                        if(res.success) {
                            Swal.fire('Terhapus!', res.message, 'success').then(() => location.reload());
                        }
                    }
                });
            }
        })
    });
</script>
@endsection