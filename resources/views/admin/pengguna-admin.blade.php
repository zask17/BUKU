@extends('layouts.admin.main')
@section('title-page', 'Daftar Pengguna')
@section('breadcrumb')
    <li class="breadcrumb-item active">Pengguna</li>
@endsection

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
                <h4 class="card-title">Daftar Pengguna Sistem</h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th> ID </th>
                                <th> Nama </th>
                                <th> Email </th>
                                <th> Role </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dataUsers as $u)
                            <tr>
                                <td> {{ $u->iduser }} </td>
                                <td> {{ $u->nama_user }} </td>
                                <td> {{ $u->email }} </td>
                                <td>
                                    <label class="badge {{ $u->role_id == 1 ? 'badge-danger' : 'badge-info' }}">
                                        {{ strtoupper($u->nama_role) }}
                                    </label>
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
@endsection

@section('js-page')
    <script>
        console.log("Welcome page loaded with Purple Admin layout.");
    </script>
@endsection