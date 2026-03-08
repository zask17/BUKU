@extends('layouts.admin.main')
@section('title-page', 'Edit Kategori')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.kategori.index') }}">Kategori</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6 grid-margin stretch-card mx-auto">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit Kategori</h4>
                    <form action="{{ route('admin.kategori.update', $kategori->idkategori) }}" method="POST" id="formEdit">
                        @csrf @method('PUT')
                        <div class="form-group">
                            <label for="nama_kategori">Nama Kategori</label>
                            <input type="text" name="nama_kategori" class="form-control" id="nama_kategori"
                                value="{{ $kategori->nama_kategori }}" required>
                        </div>
                    </form>
                    <div class="mt-4">
                        <a href="{{ route('admin.kategori.index') }}" class="btn btn-light">Batal</a>
                        <button type="button" id="btnUpdate" class="btn btn-gradient-primary me-2">
                            <span id="updateText">Update Perubahan</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js-page')
    <script>
        document.getElementById('btnUpdate').addEventListener('click', function () {
            const form = document.getElementById('formEdit');
            if (form.checkValidity()) {
                setButtonLoading(this, 'Memperbarui...');
                form.submit();
            } else {
                form.reportValidity();
            }
        });
    </script>
@endsection