@extends('layouts.admin.main')
@section('title-page', 'Edit Toko')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.toko.list') }}">Daftar Toko</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <form id="form-edit-toko">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Nama Toko</label>
                        <input type="text" class="form-control" name="nama_toko" value="{{ $toko->nama_toko }}" required>
                    </div>
                    <div class="form-group">
                        <label>Latitude</label>
                        <input type="text" class="form-control" name="latitude" value="{{ $toko->latitude }}" required>
                    </div>
                    <div class="form-group">
                        <label>Longitude</label>
                        <input type="text" class="form-control" name="longtitude" value="{{ $toko->longtitude }}" required>
                    </div>
                    <div class="form-group">
                        <label>Akurasi Toko (Meter)</label>
                        <input type="number" class="form-control" name="accuracy" value="{{ $toko->accuracy }}" required>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary">Update Toko</button>
                    <a href="{{ route('admin.toko.list') }}" class="btn btn-light">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-page')
<script>
$('#form-edit-toko').on('submit', function(e) {
    e.preventDefault();
    $.post("{{ route('admin.toko.update', $toko->idtoko) }}", $(this).serialize())
    .done(res => { if(res.success) window.location.href = res.redirect; })
    .fail(err => alert(err.responseJSON.message));
});
</script>
@endsection