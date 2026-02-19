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
    {{-- <div class="content-wrapper">
        <section class="landing-hero shadow-sm">
            <div class="container">
                <h1 class="display-4">Halo Admin</h1>
                <p class="lead text-muted mt-3">
                    Jangan lupa makan karena gak semua punya someone buat diajak makan siang.
                </p>
                <div class="mt-4">
                    @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-gradient-primary btn-lg me-2">Buka Dashboard</a>
                    @else
                    <a href="{{ route('login') }}" class="btn btn-gradient-primary btn-lg me-2">Mulai Sekarang</a>
                    @endauth
                    <a href="#features" class="btn btn-outline-custom btn-lg">Lihat Fitur Database</a>
                </div>
            </div>
        </section> --}}

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Daftar Kategori</h4>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#modalAdd">Tambah</button>
                        <div class="table-responsive">

                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Kategori</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dataKategori as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->nama_kategori }}</td>
                                            <td>
                                                <button class="btn btn-info btn-xs" data-bs-toggle="modal"
                                                    data-bs-target="#edit{{ $item->idkategori }}">Edit</button>
                                                <form action="{{ route('admin.kategori.destroy', $item->idkategori) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-danger btn-xs"
                                                        onclick="return confirm('Hapus?')">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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