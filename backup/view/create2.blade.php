@extends('layouts.admin.main')

@section('title-page', 'Tambah Customer 2')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.customer.index') }}">Customer</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah Customer 2</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header bg-gradient-info text-white">Tambah Customer (Simpan File Path)</div>
        <div class="card-body">
            <form action="{{ route('admin.customer.store2') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label>Nama Customer</label>
                        <input type="text" name="nama" class="form-control" placeholder="Input Nama" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label>Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control" rows="3"
                            placeholder="Jl. Contoh No. 123..."></textarea>
                    </div>
                </div>

                {{-- PEMILIHAN WILAYAH --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label>Provinsi</label>
                        <select id="provinsi" name="id_provinsi" class="form-control" onchange="getKotaAxios()">
                            <option value="0">Pilih Provinsi</option>
                            @foreach($provinsis as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Kota</label>
                        <select id="kota" name="id_kota" class="form-control" onchange="getKecamatanAxios()">
                            <option value="0">Pilih Kota</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Kecamatan</label>
                        <select id="kecamatan" name="id_kecamatan" class="form-control" onchange="getKelurahanAxios()">
                            <option value="0">Pilih Kecamatan</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Kelurahan</label>
                        <select id="kelurahan" name="id_kelurahan" class="form-control">
                            <option value="0">Pilih Kelurahan</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Kode Pos</label>
                        <input type="text" name="kode_pos" class="form-control" placeholder="60111" maxlength="5" required>
                    </div>
                </div>

                {{-- Bagian Kamera --}}
                <div class="row mt-4">
                    <div class="col-md-6 text-center">
                        <label class="form-label fw-bold">Ambil Foto Customer</label>
                        <div id="my_camera" class="mx-auto border shadow-sm" style="width: 320px; height: 240px;"></div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-info text-white" onclick="take_snapshot()">
                                <i class="mdi mdi-camera"></i> Ambil Foto
                            </button>
                            <button type="button" class="btn btn-secondary" id="retake-btn" onclick="retake_snapshot()" style="display:none;">
                                <i class="mdi mdi-refresh"></i> Ulang Foto
                            </button>
                        </div>
                        <input type="hidden" name="image" class="image-tag">
                        <small class="text-danger d-block mt-2" id="camera-error" style="display:none;"></small>
                    </div>
                    <div class="col-md-6 text-center">
                        <label class="form-label fw-bold">Preview Foto</label>
                        <div id="results" class="border shadow-sm" style="height: 240px; width: 320px; margin: 0 auto; display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
                            <span class="text-muted">Foto akan ditampilkan di sini...</span>
                        </div>
                    </div>
                </div>

                <div class="mt-5 border-top pt-3">
                    <button type="submit" class="btn btn-info text-white">Simpan Data (Path)</button>
                    <a href="{{ route('admin.customer.index') }}" class="btn btn-light">Batal</a>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPT UNTUK WEBCAM DAN WILAYAH --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // --- WEBCAM INITIALIZATION ---
        Webcam.set({
            width: 320,
            height: 240,
            image_format: 'png',
            jpeg_quality: 90,
            facingMode: 'user'
        });

        // Coba attach kamera
        Webcam.attach('#my_camera', function(code) {
            console.log('Kamera berhasil diakses');
        }, function(err) {
            console.error('Error: ' + err);
            document.getElementById('camera-error').textContent = 'Gagal mengakses kamera. Pastikan browser telah mengizinkan akses kamera.';
            document.getElementById('camera-error').style.display = 'block';
        });

        function take_snapshot() {
            const imageInput = document.querySelector('.image-tag').value;
            
            if (imageInput) {
                if (!confirm('Anda sudah mengambil foto. Alih-alih mengambil foto baru?')) {
                    return;
                }
            }

            Webcam.snap(function (data_uri) {
                document.querySelector('.image-tag').value = data_uri;
                document.getElementById('results').innerHTML = `<img src="${data_uri}" class="rounded" style="max-width: 100%; max-height: 100%; object-fit: contain;"/>`;
                document.getElementById('retake-btn').style.display = 'inline-block';
            }, function(err) {
                console.error('Error taking snapshot:', err);
                alert('Gagal mengambil foto. Silakan coba lagi.');
            });
        }

        function retake_snapshot() {
            document.querySelector('.image-tag').value = '';
            document.getElementById('results').innerHTML = '<span class="text-muted">Foto akan ditampilkan di sini...</span>';
            document.getElementById('retake-btn').style.display = 'none';
            Webcam.reset();
            Webcam.attach('#my_camera');
        }

        // --- AXIOS WILAYAH LOGIC ---
        function reset(ids) {
            ids.forEach(id => {
                document.getElementById(id).innerHTML = `<option value="0">Pilih ${id.charAt(0).toUpperCase() + id.slice(1)}</option>`;
            });
        }

        function getKotaAxios() {
            const id = document.getElementById('provinsi').value;
            reset(['kota', 'kecamatan', 'kelurahan']);
            if (id != 0) {
                axios.post("{{ route('wilayah.getKota') }}", { id: id, _token: "{{ csrf_token() }}" })
                    .then(res => {
                        res.data.data.forEach(item => {
                            document.getElementById('kota').innerHTML += `<option value="${item.id}">${item.name}</option>`;
                        });
                    })
                    .catch(err => console.error(err));
            }
        }

        function getKecamatanAxios() {
            const id = document.getElementById('kota').value;
            reset(['kecamatan', 'kelurahan']);
            if (id != 0) {
                axios.post("{{ route('wilayah.getKecamatan') }}", { id: id, _token: "{{ csrf_token() }}" })
                    .then(res => {
                        res.data.data.forEach(item => {
                            document.getElementById('kecamatan').innerHTML += `<option value="${item.id}">${item.name}</option>`;
                        });
                    })
                    .catch(err => console.error(err));
            }
        }

        function getKelurahanAxios() {
            const id = document.getElementById('kecamatan').value;
            reset(['kelurahan']);
            if (id != 0) {
                axios.post("{{ route('wilayah.getKelurahan') }}", { id: id, _token: "{{ csrf_token() }}" })
                    .then(res => {
                        res.data.data.forEach(item => {
                            document.getElementById('kelurahan').innerHTML += `<option value="${item.id}">${item.name}</option>`;
                        });
                    })
                    .catch(err => console.error(err));
            }
        }

        // Validasi form sebelum submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const imageInput = document.querySelector('.image-tag').value;
            if (!imageInput) {
                e.preventDefault();
                alert('Silakan ambil foto terlebih dahulu!');
            }
        });
    </script>
@endsection