@extends($layout)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('barang.index') }}">Daftar Barang</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Barang</li>
@endsection

@section('content')
    <div class="container">
        <h1 class="mb-4">Edit Barang</h1>

        <form action="{{ route('barang.update', $barang->id_barang) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nama" class="form-label">Nama Barang</label>
                <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" 
                       value="{{ old('nama', $barang->nama) }}" required maxlength="50">
                @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="harga" class="form-label">Harga (Rp)</label>
                <input type="number" name="harga" id="harga" class="form-control @error('harga') is-invalid @enderror" 
                       value="{{ old('harga', $barang->harga) }}" required min="50000">
                @error('harga')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <a href="{{ route('barang.index') }}" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary">Update Barang</button>
        </form>
    </div>
@endsection