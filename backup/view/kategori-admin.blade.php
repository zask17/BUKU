@extends('layouts.admin.main')

@section('title-page', 'Manajemen Kategori')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Kategori</li>
@endsection

@section('style-page')
    <style>
        .badge-gradient-info {
            background: linear-gradient(to right, #36d1dc, #5b86e5);
            color: white;
        }
        .id-badge {
            font-size: 0.75rem;
            background-color: #e9ecef;
            color: #495057;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title">Daftar Kategori</h4>
                        <button type="button" class="btn btn-gradient-primary btn-fw" data-bs-toggle="modal"
                            data-bs-target="#modalTambahKategori">
                            <i class="mdi mdi-plus"></i> Tambah Kategori
                        </button>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
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
                                            <label class="badge badge-gradient-info">
                                                {{ $kategori->nama_kategori }}
                                            </label>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-inverse-dark btn-icon" data-bs-toggle="modal"
                                                data-bs-target="#modalEditKategori{{ $kategori->idkategori }}">
                                                <i class="mdi mdi-pencil"></i>
                                            </button>

                                            <form action="{{ route('admin.kategori.destroy', $kategori->idkategori) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-inverse-danger btn-icon"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?\n\nJika ada buku yang masih menggunakan kategori ini, statusnya akan menjadi Umum.')">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- Modal Edit -->
                                    <div class="modal fade" id="modalEditKategori{{ $kategori->idkategori }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Kategori</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.kategori.update', $kategori->idkategori) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>ID Kategori</label>
                                                            <input type="text" class="form-control" value="KAT-{{ str_pad($kategori->idkategori, 3, '0', STR_PAD_LEFT) }}" readonly>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Nama Kategori</label>
                                                            <input type="text" name="nama_kategori" class="form-control"
                                                                value="{{ $kategori->nama_kategori }}" required
                                                                placeholder="Contoh: Fiksi, Non-Fiksi, Komik">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-gradient-primary">Simpan Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            Belum ada data kategori.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Kategori -->
    <div class="modal fade" id="modalTambahKategori" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kategori Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.kategori.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Kategori</label>
                            <input type="text" name="nama_kategori" class="form-control" required
                                placeholder="Masukkan nama kategori (contoh: Novel, Pendidikan, Agama)">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-gradient-primary">Simpan Kategori</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js-page')
    <script>
        console.log("Halaman Manajemen Kategori Berhasil Dimuat.");
    </script>
@endsection