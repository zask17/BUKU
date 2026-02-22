@extends('layouts.visitor.main')

@section('title-page', 'Daftar Kategori')

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
                                </tr>
                            </thead>
                            
                            <tbody>
                                @forelse($dataKategori as $key => $kategori)
                                    <tr>
                                        <td> {{ $key + 1 }} </td>
                                        <td>
                                            <span class="badge id-badge">
                                                KAT-{{ str_pad($kategori->idkategori, 3, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </td>
                                        <td>
                                            <label class="badge badge-gradient-info">
                                                {{ $kategori->nama_kategori }}
                                            </label>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
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
@endsection

@section('js-page')
    <script>
        console.log("Halaman Daftar Kategori Visitor Berhasil Dimuat.");
    </script>
@endsection