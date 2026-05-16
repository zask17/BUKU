@extends('layouts.admin.main')
@section('title-page', 'Tambah Toko')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.toko.index') }}">Daftar Toko</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <form id="form-toko">
                    @csrf
                    <div class="form-group">
                        <label>ID Toko</label>
                        <input type="text" class="form-control bg-light" name="idtoko" value="{{ $nextId }}" readonly>
                    </div>

                    <div class="form-group">
                        <label>Nama Toko</label>
                        <input type="text" class="form-control" name="nama_toko" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Latitude</label>
                        <input type="text" class="form-control" id="lat" name="latitude" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Longitude</label>
                        <input type="text" class="form-control" id="long" name="longtitude" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Akurasi Toko (Meter)</label>
                        <input type="number" class="form-control" id="acc" name="accuracy" value="30" required>
                    </div>

                    <button type="button" id="btn-location" class="btn btn-info btn-sm mb-3" onclick="getLocation()">
                        <i class="mdi mdi-map-marker"></i> Ambil Lokasi Saat Ini
                    </button>
                    
                    <hr>
                    <button type="submit" class="btn btn-primary">Simpan Toko</button>
                    <a href="{{ route('admin.toko.index') }}" class="btn btn-light">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-page')
<script>
function setButtonLoading(button, loading, text) {
    if (!button) return;
    if (loading) {
        button.dataset.originalHtml = button.innerHTML;
        button.disabled = true;
        button.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>${text}`;
    } else {
        button.disabled = false;
        button.innerHTML = button.dataset.originalHtml || button.innerHTML;
    }
}

function getLocation() {
    const button = document.getElementById('btn-location');
    setButtonLoading(button, true, 'Mencari lokasi...');

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                document.getElementById('lat').value = pos.coords.latitude;
                document.getElementById('long').value = pos.coords.longitude;
                document.getElementById('acc').value = Math.round(pos.coords.accuracy);
                setButtonLoading(button, false);
            },
            (err) => {
                if (err.code === err.PERMISSION_DENIED) {
                    alert('Gagal: Izin lokasi ditolak browser.');
                }
                setButtonLoading(button, false);
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    } else {
        alert('Geolocation tidak didukung browser Anda.');
        setButtonLoading(button, false);
    }
}

$('#form-toko').on('submit', function(e) {
    e.preventDefault();
    const submitButton = $(this).find('button[type=submit]')[0];
    setButtonLoading(submitButton, true, 'Memproses...');

    $.post("{{ route('admin.toko.store') }}", $(this).serialize())
    .done(res => { 
        if(res.success) window.location.href = res.redirect; 
    })
    .fail(err => {
        alert(err.responseJSON.message || 'Gagal menyimpan data.');
        setButtonLoading(submitButton, false);
    });
});
</script>
@endsection