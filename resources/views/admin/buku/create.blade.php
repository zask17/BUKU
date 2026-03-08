@extends('layouts.admin.main')

@section('title-page', 'Tambah Buku')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.buku.index') }}">Buku</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah Buku Baru</h4>
                <p class="card-description"> Masukkan data buku secara lengkap </p>
                
                <form id="formTambahBuku" class="forms-sample" action="{{ route('admin.buku.store') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label>Kode Buku</label>
                        <input type="text" class="form-control" name="kode" placeholder="Contoh: NV-01" required>
                    </div>

                    <div class="form-group">
                        <label>Judul Buku</label>
                        <input type="text" class="form-control" name="judul" placeholder="Masukkan judul buku" required>
                    </div>

                    <div class="form-group">
                        <label>Pengarang</label>
                        <input type="text" class="form-control" name="pengarang" placeholder="Nama penulis" required>
                    </div>

                    <div class="form-group">
                        <label>Kategori</label>
                        <select class="form-control" name="idkategori" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($dataKategori as $kat)
                                <option value="{{ $kat->idkategori }}">{{ $kat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('admin.buku.index') }}" class="btn btn-light">Batal</a>
                        <button type="button" id="btnSubmitBuku" class="btn btn-gradient-primary me-2">
                            <span id="btnText">Simpan Buku</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-page')
<script>
    document.getElementById('btnSubmitBuku').addEventListener('click', function() {
        const form = document.getElementById('formTambahBuku');
        const btnText = document.getElementById('btnText');
        
        if (form.checkValidity()) {
            // Disable button dan tampilkan loader
            this.disabled = true;
            btnText.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...`;
            form.submit();
        } else {
            form.reportValidity();
        }
    });
</script>
@endsection