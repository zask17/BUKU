@extends('layouts.visitor.main')

@section('title-page', 'Daftar Buku')

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
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Data buku belum tersedia.</td>
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
        console.log("Halaman Daftar Buku Visitor Berhasil Dimuat.");
    </script>
@endsection