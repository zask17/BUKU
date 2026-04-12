@extends('layouts.guest.main')

@section('title-page', 'Pembayaran Berhasil')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('kantin.index') }}">Kantin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Selesai</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card text-center">
            <div class="card-body py-5">
                <i class="mdi mdi-check-circle text-success" style="font-size: 72px;"></i>
                <h4 class="font-weight-bold mt-3">Pembayaran Berhasil!</h4>
                <p class="text-muted">Pesananmu sedang diproses oleh vendor. Terima kasih!</p>
                <a href="{{ route('kantin.index') }}" class="btn btn-gradient-primary mt-2">
                    <i class="mdi mdi-silverware-fork-knife mr-1"></i> Pesan Lagi
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-page')
    <script>console.log("Pembayaran berhasil.");</script>
@endsection