@extends('layouts.admin.main')

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
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daftar Buku Tersedia</h4>
                    {{-- <p class="card-description"> Kelola koleksi buku yang terdaftar dalam sistem </p> --}}
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th> No </th>
                                    <th> Judul Buku </th>
                                    <th> Kategori </th>
                                    <th> Penulis </th>
                                    {{-- <th> Stok </th>
                                    <th class="text-center"> Aksi </th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataBuku as $key => $buku)
                                    <tr>
                                        <td> {{ $key + 1 }} </td>
                                        <td class="font-weight-bold"> {{ $buku->judul }} </td>
                                        <td>
                                            <label
                                                class="badge badge-gradient-info">{{ $buku->kategori->nama ?? 'Umum' }}</label>
                                        </td>
                                        <td> {{ $buku->pengarang }} </td>
                                        {{-- <td> {{ $buku->stok }} </td> --}}
                                        {{-- <td class="text-center">
                                            <button type="button" class="btn btn-inverse-dark btn-icon">
                                                <i class="mdi mdi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-inverse-danger btn-icon">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </td> --}}
                                    </tr>
                                @endforeach
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
        console.log("Welcome page loaded with Purple Admin layout.");
    </script>
@endsection