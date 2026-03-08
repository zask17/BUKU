@extends('layouts.admin.main')

@section('title-page', 'Edit Buku')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.buku.index') }}">Buku</a></li>
    <li class="breadcrumb-item active" aria-current="page">
        Edit <i class="mdi mdi-pencil-outline icon-sm text-primary align-middle"></i>
    </li>
@endsection

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Data Buku</h4>
                <p class="card-description"> Perbarui informasi buku yang diperlukan </p>
                
                <form id="formEditBuku" class="forms-sample" action="{{ route('admin.buku.update', $buku->idbuku) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label>Kode Buku</label>
                        <input type="text" class="form-control" name="kode" value="{{ $buku->kode }}" required>
                    </div>

                    <div class="form-group">
                        <label>Judul Buku</label>
                        <input type="text" class="form-control" name="judul" value="{{ $buku->judul }}" required>
                    </div>

                    <div class="form-group">
                        <label>Pengarang</label>
                        <input type="text" class="form-control" name="pengarang" value="{{ $buku->pengarang }}" required>
                    </div>

                    <div class="form-group">
                        <label>Kategori</label>
                        <select class="form-control" name="idkategori" required>
                            @foreach($dataKategori as $kat)
                                <option value="{{ $kat->idkategori }}" {{ $buku->idkategori == $kat->idkategori ? 'selected' : '' }}>
                                    {{ $kat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('admin.buku.index') }}" class="btn btn-light">Batal</a>
                        <button type="button" id="btnUpdateBuku" class="btn btn-gradient-primary me-2">
                            <span id="btnText">Update Perubahan</span>
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
    document.getElementById('btnUpdateBuku').addEventListener('click', function() {
        const form = document.getElementById('formEditBuku');
        const btnText = document.getElementById('btnText');

        // Menggunakan checkValidity HTML5
        if (form.checkValidity()) {
            // Memberi feedback spinner dan menghindari double submit
            this.disabled = true;
            btnText.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memperbarui...`;
            form.submit();
        } else {
            // Menunjukkan input yang masih kosong
            form.reportValidity();
        }
    });
</script>
@endsection