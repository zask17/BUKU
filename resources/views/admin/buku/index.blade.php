@extends('layouts.admin.main')

@section('title-page', 'Manajemen Buku')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Buku</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">Data Buku Tersedia</h4>
                    <a href="{{ route('admin.buku.create') }}" class="btn btn-gradient-primary btn-fw">
                        <i class="mdi mdi-plus"></i> Tambah Buku
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
                                <th> Kode </th>
                                <th> Judul Buku </th>
                                <th> Kategori </th>
                                <th> Penulis </th>
                                <th class="text-center"> Aksi </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dataBuku as $key => $buku)
                                <tr>
                                    <td> {{ $key + 1 }} </td>
                                    <td class="font-weight-bold"> {{ $buku->kode }} </td>
                                    <td> {{ $buku->judul }} </td>
                                    <td>
                                        <label class="badge badge-info">
                                            {{ $buku->nama_kategori ?? 'Umum' }}
                                        </label>
                                    </td>
                                    <td> {{ $buku->pengarang }} </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center" style="gap: 8px;">
                                            <a href="{{ route('admin.buku.edit', $buku->idbuku) }}" 
                                               class="btn btn-inverse-dark btn-icon d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>

                                            <form action="{{ route('admin.buku.destroy', $buku->idbuku) }}" method="POST" class="m-0 form-delete">
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
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Data buku belum tersedia.</td>
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
    document.querySelectorAll('.form-delete').forEach(form => {
        form.addEventListener('submit', function(e) {
            // Konfirmasi sebelum hapus
            if (!confirm('Apakah Anda yakin ingin menghapus buku ini?')) {
                e.preventDefault();
                return false;
            }
            
            // Efek loading pada tombol hapus
            const btn = this.querySelector('.btn-delete');
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`;
        });
    });
</script>
@endsection