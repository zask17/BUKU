@extends('layouts.vendor.main')

@section('title-page', 'Edit Pesanan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('vendor.pesanan.index') }}">Pesanan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Pesanan</li>
@endsection

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0"><i class="mdi mdi-pencil"></i> Edit Menu</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('vendor.menu.update', $menu) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="nama_menu"><strong>Nama Menu</strong></label>
                            <input type="text" class="form-control @error('nama_menu') is-invalid @enderror" 
                                   id="nama_menu" name="nama_menu" placeholder="Contoh: Nasi Bakar" 
                                   value="{{ old('nama_menu', $menu->nama_menu) }}" required>
                            @error('nama_menu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="harga"><strong>Harga (Rp)</strong></label>
                            <input type="number" class="form-control @error('harga') is-invalid @enderror" 
                                   id="harga" name="harga" placeholder="Contoh: 15000" 
                                   value="{{ old('harga', $menu->harga) }}" min="500" required>
                            <small class="form-text text-muted">Minimum harga: Rp 500</small>
                            @error('harga')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="gambar"><strong>Gambar Menu (Opsional)</strong></label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('gambar') is-invalid @enderror" 
                                       id="gambar" name="gambar" accept="image/*" onchange="previewImage(this)">
                                <label class="custom-file-label" for="gambar">
                                    {{ $menu->path_gambar ? 'Ganti gambar...' : 'Pilih gambar...' }}
                                </label>
                            </div>
                            <small class="form-text text-muted">Format: JPG, PNG (Max 2MB)</small>
                            @error('gambar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if ($menu->path_gambar)
                            <div class="form-group">
                                <label><strong>Gambar Saat Ini</strong></label>
                                <div>
                                    <img src="{{ asset('storage/' . $menu->path_gambar) }}" alt="{{ $menu->nama_menu }}" 
                                         style="max-width: 200px; max-height: 200px; border-radius: 5px;">
                                </div>
                            </div>
                        @endif

                        <div id="preview-container" style="display:none;">
                            <div class="form-group">
                                <label><strong>Preview Gambar Baru</strong></label>
                                <img id="preview" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 5px;">
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="mdi mdi-information"></i> <strong>Vendor:</strong> {{ $menu->vendor->nama_vendor }}
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success">
                                <i class="mdi mdi-check"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('vendor.menu.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-close"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('preview-container').style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
        
        // Update label
        var fileName = input.files[0].name;
        input.nextElementSibling.textContent = fileName;
    }
}
</script>
@endsection
