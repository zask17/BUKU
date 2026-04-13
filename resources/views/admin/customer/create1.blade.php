@extends('layouts.admin.main')

@section('title-page', 'Tambah Customer 1')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.customer.index') }}">Customer</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah Customer 1</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-gradient-primary text-white">Tambah Customer (Simpan BLOB)</div>
    <div class="card-body">
        <form action="{{ route('admin.customer.store1') }}" method="POST" id="customerForm" novalidate>
            @csrf

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="fw-bold">Nama Customer</label>
                    <input type="text" name="nama" class="form-control" placeholder="Input Nama" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="fw-bold">Alamat Lengkap</label>
                    <textarea name="alamat" class="form-control" rows="2" placeholder="Jl. Contoh No. 123..." required></textarea>
                </div>
            </div>

            {{-- PEMILIHAN WILAYAH --}}
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label>Provinsi</label>
                    <select id="provinsi" name="id_provinsi" class="form-control" onchange="getKotaAxios()" required>
                        <option value="">Pilih Provinsi</option>
                        @foreach($provinsis as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Kota</label>
                    <select id="kota" name="id_kota" class="form-control" onchange="getKecamatanAxios()" required>
                        <option value="">Pilih Kota</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Kecamatan</label>
                    <select id="kecamatan" name="id_kecamatan" class="form-control" onchange="getKelurahanAxios()" required>
                        <option value="">Pilih Kecamatan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Kelurahan</label>
                    <select id="kelurahan" name="id_kelurahan" class="form-control" required>
                        <option value="">Pilih Kelurahan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Kode Pos</label>
                    <input type="text" id="kode_pos" name="kode_pos" class="form-control" placeholder="60111" maxlength="5" minlength="5" required>
                </div>
            </div>

            <hr>

            <div class="row mt-4">
                <div class="col-md-6 text-center">
                    <label class="form-label fw-bold"><i class="mdi mdi-video"></i> Kamera Live</label>
                    <div id="my_camera" class="mx-auto border bg-dark shadow-sm" style="width: 320px; height: 240px;"></div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-primary text-white" onclick="take_snapshot()">
                            <i class="mdi mdi-camera"></i> Ambil Foto
                        </button>
                    </div>
                    {{-- Input hidden foto diberi required --}}
                    <input type="hidden" name="image" id="image_tag" required>
                </div>
                <div class="col-md-6 text-center">
                    <label class="form-label fw-bold"><i class="mdi mdi-image"></i> Preview Hasil</label>
                    <div id="results" class="border shadow-sm mx-auto" style="height: 240px; width: 320px; display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
                        <span class="text-muted small">Hasil foto akan muncul di sini</span>
                    </div>
                </div>
            </div>

            <div class="mt-5 border-top pt-3 text-end">
                <a href="{{ route('admin.customer.index') }}" class="btn btn-light me-2">Batal</a>
                <button type="submit" class="btn btn-success px-4">Simpan Data (BLOB)</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        Webcam.set({ width: 320, height: 240, image_format: 'png', jpeg_quality: 90 });
        Webcam.attach('#my_camera');
    });

    function take_snapshot() {
        Webcam.snap(function(data_uri) {
            document.getElementById('image_tag').value = data_uri;
            document.getElementById('results').innerHTML = `<img src="${data_uri}" class="img-fluid rounded shadow-sm" style="max-height: 100%;"/>`;
        });
    }

    function reset(ids) {
        ids.forEach(id => {
            document.getElementById(id).innerHTML = `<option value="">Pilih ${id.charAt(0).toUpperCase() + id.slice(1)}</option>`;
        });
    }

    function getKotaAxios() {
        const id = document.getElementById('provinsi').value;
        reset(['kota', 'kecamatan', 'kelurahan']);
        if (id) {
            axios.post("{{ route('wilayah.getKota') }}", { id: id, _token: "{{ csrf_token() }}" })
                .then(res => {
                    let opt = '<option value="">Pilih Kota</option>';
                    res.data.data.forEach(item => opt += `<option value="${item.id}">${item.name}</option>`);
                    document.getElementById('kota').innerHTML = opt;
                });
        }
    }

    function getKecamatanAxios() {
        const id = document.getElementById('kota').value;
        reset(['kecamatan', 'kelurahan']);
        if (id) {
            axios.post("{{ route('wilayah.getKecamatan') }}", { id: id, _token: "{{ csrf_token() }}" })
                .then(res => {
                    let opt = '<option value="">Pilih Kecamatan</option>';
                    res.data.data.forEach(item => opt += `<option value="${item.id}">${item.name}</option>`);
                    document.getElementById('kecamatan').innerHTML = opt;
                });
        }
    }

    function getKelurahanAxios() {
        const id = document.getElementById('kecamatan').value;
        reset(['kelurahan']);
        if (id) {
            axios.post("{{ route('wilayah.getKelurahan') }}", { id: id, _token: "{{ csrf_token() }}" })
                .then(res => {
                    let opt = '<option value="">Pilih Kelurahan</option>';
                    res.data.data.forEach(item => opt += `<option value="${item.id}">${item.name}</option>`);
                    document.getElementById('kelurahan').innerHTML = opt;
                });
        }
    }

    document.getElementById('customerForm').addEventListener('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            this.reportValidity(); // Menampilkan tooltip penunjuk
            
            // Cek khusus foto jika teks sudah valid tapi foto kosong
            const foto = document.getElementById('image_tag').value;
            if(!foto) {
                alert('Silakan ambil foto terlebih dahulu!');
            }
            return;
        }
    });
</script>
@endsection