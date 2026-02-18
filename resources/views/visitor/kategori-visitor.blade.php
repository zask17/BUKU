@extends('layouts.visitor.main')

@section('style-page')
    <style>
        .landing-hero {
            text-align: center;
            padding: 60px 20px;
            background: #f8f9fa;
            border-radius: 15px;
            margin-bottom: 30px;
        }

        .landing-hero h1 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #343a40;
        }

        .cta-section {
            background: linear-gradient(to right, #19d895, #21d0f5);
            color: white;
            padding: 50px 20px;
            border-radius: 15px;
            text-align: center;
            margin-top: 30px;
        }

        .cta-section h2 {
            color: white;
        }

        .btn-outline-custom {
            border: 2px solid #19d895;
            color: #19d895;
            transition: 0.3s;
        }

        .btn-outline-custom:hover {
            background: #19d895;
            color: white;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-wrapper">
            <div class="page-header">
                {{-- <h3 class="page-title">
                    <span class="page-title-icon bg-gradient-primary text-white me-2">
                        <i class="mdi mdi-home"></i>
                    </span> Dashboard Statistik
                </h3> --}}
            </div>
            {{-- Sisipkan kode ini di bawah penutup div class="row" yang berisi statistik --}}

            <div class="row">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Daftar Kategori Buku</h4>
                            {{-- <p class="card-description"> Kelompok buku yang tersedia di perpustakaan </p> --}}
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="bg-light">
                                            <th style="width: 50px;"> No </th>
                                            {{-- <th> ID Kategori </th> --}}
                                            <th> Nama Kategori </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($allKategori as $key => $kat)
                                            <tr>
                                                <td> {{ $key + 1 }} </td>
                                                {{-- <td> <code>#{{ $kat->idkategori }}</code> </td> --}}
                                                <td class="font-weight-bold"> {{ $kat->nama_kategori }} </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">Belum ada data kategori.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </section>


        </div>
@endsection

    @section('js-page')
        <script>
            console.log("Welcome page loaded with Purple Admin layout.");
        </script>
    @endsection