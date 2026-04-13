@extends('layouts.vendor.main')

@section('title-page', 'Menu')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Menu</li>
@endsection

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="mdi mdi-menu"></i> Daftar Menu {{ $vendor->nama_vendor }}</h2>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('vendor.menu.create') }}" class="btn btn-primary">
                <i class="mdi mdi-plus"></i> Tambah Menu
            </a>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle"></i> {{ $message }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle"></i> {{ $message }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($menus->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nama Menu</th>
                        <th>Harga</th>
                        <th>Gambar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($menus as $menu)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $menu->nama_menu }}</td>
                            <td>
                                <span class="badge badge-success">Rp {{ number_format($menu->harga, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                @if ($menu->path_gambar)
                                    <img src="{{ asset('storage/' . $menu->path_gambar) }}" alt="{{ $menu->nama_menu }}" 
                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                @else
                                    <span class="badge badge-secondary">Tidak ada</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('vendor.menu.edit', $menu) }}" class="btn btn-sm btn-info" title="Edit">
                                    <i class="mdi mdi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('vendor.menu.destroy', $menu) }}" 
                                      style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="mdi mdi-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                <i class="mdi mdi-information"></i> Belum ada menu. 
                                <a href="{{ route('vendor.menu.create') }}">Tambah sekarang</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-info">
            <i class="mdi mdi-information"></i> Belum ada menu. 
            <a href="{{ route('vendor.menu.create') }}" class="btn btn-primary btn-sm">Tambah Menu Baru</a>
        </div>
    @endif
</div>

<style>
    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
    }
</style>
@endsection
