@extends('layouts.admin.main')
@section('title-page', 'Tambah Kategori')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.kategori.index') }}">Kategori</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6 grid-margin stretch-card mx-auto">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Form Tambah Kategori</h4>
                <form action="{{ route('admin.kategori.store') }}" method="POST" id="formKategori">
                    @csrf
                    <div class="form-group">
                        <label for="nama_kategori">Nama Kategori</label>
                        <input type="text" name="nama_kategori" class="form-control" id="nama_kategori" placeholder="Contoh: Teknologi" required>
                    </div>
                </form>
                <div class="mt-4">
                    <a href="{{ route('admin.kategori.index') }}" class="btn btn-light">Batal</a>
                    <button type="button" id="btnSubmit" class="btn btn-gradient-primary me-2">
                        <span id="btnText">Simpan Kategori</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-page')
<script>
    document.getElementById('btnSubmit').addEventListener('click', function() {
        const form = document.getElementById('formKategori');
        if (form.checkValidity()) {
            this.disabled = true;
            document.getElementById('btnText').innerHTML = `<span class="spinner-border spinner-border-sm"></span> Menyimpan...`;
            form.submit();
        } else {
            form.reportValidity();
        }
    });
</script>
@endsection