@extends('layouts.guest.main')

@section('title-page', 'Menunggu Pembayaran')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('kantin.index') }}">Kantin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Pending</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card text-center">
            <div class="card-body py-5">
                <i class="mdi mdi-clock-outline text-warning" style="font-size: 72px;"></i>
                <h4 class="font-weight-bold mt-3">Menunggu Pembayaran</h4>
                <p class="text-muted">Selesaikan pembayaranmu sesuai instruksi yang telah dikirim.</p>
                <a href="{{ route('kantin.index') }}" class="btn btn-gradient-primary mt-2">
                    <i class="mdi mdi-arrow-left mr-1"></i> Kembali ke Menu
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-page')
    <script>console.log("Pembayaran pending.");</script>
@endsection