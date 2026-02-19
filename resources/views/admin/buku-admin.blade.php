@extends('layouts.admin.main')

@section('title-page', 'Manajemen Buku')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Buku</li>
@endsection

@section('style-page')
    <style>
        .badge-gradient-info {
            background: linear-gradient(to right, #36d1dc, #5b86e5);
            color: white;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title">Daftar Buku Tersedia</h4>
                        <button type="button" class="btn btn-gradient-primary btn-fw" data-bs-toggle="modal"
                            data-bs-target="#modalTambahBuku">
                            <i class="mdi mdi-plus"></i> Tambah Buku
                        </button>
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
                                        <td> <code>{{ $buku->kode }}</code> </td>
                                        <td class="font-weight-bold"> {{ $buku->judul }} </td>
                                        <td>
                                            <label class="badge badge-gradient-info">
                                                {{ $buku->nama_kategori ?? 'Umum' }}
                                            </label>
                                        </td>
                                        <td> {{ $buku->pengarang }} </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-inverse-dark btn-icon" data-bs-toggle="modal"
                                                data-bs-target="#modalEditBuku{{ $buku->idbuku }}">
                                                <i class="mdi mdi-pencil"></i>
                                            </button>

                                            <form action="{{ route('admin.buku.destroy', $buku->idbuku) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-inverse-danger btn-icon"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="modalEditBuku{{ $buku->idbuku }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Buku</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.buku.update', $buku->idbuku) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>Kode Buku</label>
                                                            <input type="text" name="kode" class="form-control"
                                                                value="{{ $buku->kode }}" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Judul Buku</label>
                                                            <input type="text" name="judul" class="form-control"
                                                                value="{{ $buku->judul }}" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Pengarang</label>
                                                            <input type="text" name="pengarang" class="form-control"
                                                                value="{{ $buku->pengarang }}" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Kategori</label>
                                                            <select name="idkategori" class="form-control" required>
                                                                @foreach($dataKategori as $kat)
                                                                    <option value="{{ $kat->idkategori }}" {{ $buku->idkategori == $kat->idkategori ? 'selected' : '' }}>
                                                                        {{ $kat->nama_kategori }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-gradient-primary">Simpan
                                                            Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Data buku belum tersedia.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambahBuku" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Buku Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.buku.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Kode Buku</label>
                            <input type="text" name="kode" class="form-control" placeholder="Contoh: B-001" required>
                        </div>
                        <div class="form-group">
                            <label>Judul Buku</label>
                            <input type="text" name="judul" class="form-control" placeholder="Masukkan judul buku" required>
                        </div>
                        <div class="form-group">
                            <label>Pengarang</label>
                            <input type="text" name="pengarang" class="form-control" placeholder="Nama penulis" required>
                        </div>
                        <div class="form-group">
                            <label>Kategori</label>
                            <select name="idkategori" class="form-control" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($dataKategori as $kat)
                                    <option value="{{ $kat->idkategori }}">{{ $kat->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-gradient-primary">Simpan Buku</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js-page')
    <script>
        console.log("Halaman Manajemen Buku Berhasil Dimuat.");
    </script>
@endsection