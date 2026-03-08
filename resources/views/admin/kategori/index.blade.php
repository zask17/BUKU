@extends('layouts.admin.main')

@section('title-page', 'Manajemen Kategori')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Kategori</li>
@endsection

@section('style-page')
    <style>
        .badge-gradient-info { background: linear-gradient(to right, #36d1dc, #5b86e5); color: white; }
        .id-badge { font-size: 0.75rem; background-color: #e9ecef; color: #495057; }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title">Daftar Kategori</h4>
                        <a href="{{ route('admin.kategori.create') }}" class="btn btn-gradient-primary btn-fw">
                            <i class="mdi mdi-plus"></i> Tambah Kategori
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th> No </th>
                                    <th> ID Kategori </th>
                                    <th> Nama Kategori </th>
                                    <th class="text-center"> Aksi </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dataKategori as $key => $kategori)
                                    <tr>
                                        <td> {{ $key + 1 }} </td>
                                        <td>
                                            <span class="badge id-badge">KAT-{{ str_pad($kategori->idkategori, 3, '0', STR_PAD_LEFT) }}</span>
                                        </td>
                                        <td>
                                            <label class="badge badge-gradient-info">{{ $kategori->nama_kategori }}</label>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center align-items-center" style="gap: 8px;">
                                                <a href="{{ route('admin.kategori.edit', $kategori->idkategori) }}" 
                                                   class="btn btn-inverse-dark btn-icon d-flex align-items-center justify-content-center">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>

                                                <form action="{{ route('admin.kategori.destroy', $kategori->idkategori) }}" method="POST" class="m-0 form-delete">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-inverse-danger btn-icon d-flex align-items-center justify-content-center btn-delete">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4">Belum ada data.</td></tr>
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
    document.querySelectorAll('.form-delete').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Yakin ingin menghapus kategori ini?')) {
                e.preventDefault();
                return false;
            }
            const btn = this.querySelector('.btn-delete');
            setButtonLoading(btn, '<i class="mdi mdi-delete"></i>');
        });
    });
</script>
@endsection