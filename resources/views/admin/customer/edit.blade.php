@extends('layouts.admin.main')

@section('title-page', 'Edit Customer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.customer.index') }}">Customer</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Customer</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header bg-gradient-warning text-white">Edit Data Customer</div>
        <div class="card-body">
            <form action="{{ route('admin.customer.update', $customer->idcustomer) }}" method="POST" id="editForm" novalidate>
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="fw-bold">Nama Customer</label>
                        <input type="text" name="nama_customer" class="form-control" value="{{ $customer->nama_customer }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="fw-bold">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control" rows="3" required>{{ $customer->alamat }}</textarea>
                    </div>
                </div>

                {{-- PEMILIHAN WILAYAH --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label>Provinsi</label>
                        <select id="provinsi" name="id_provinsi" class="form-control" onchange="getKotaAxios()" required>
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinsis as $p)
                                <option value="{{ $p->id }}" {{ $customer->id_provinsi == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Kota/Kabupaten</label>
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
                        <input type="text" name="kode_pos" class="form-control" value="{{ $customer->kode_pos }}" maxlength="5" minlength="5" required>
                    </div>
                </div>

                <hr>

                <div class="row mt-4">
                    <div class="col-md-4 text-center">
                        <label class="fw-bold">Foto Saat Ini</label>
                        <div class="border rounded p-2 d-flex align-items-center justify-content-center" style="height: 240px; background: #f8f9fa;">
                            @if($customer->foto_blob)
                                @php
                                    $blobData = $customer->foto_blob;
                                    if (is_string($blobData) && strpos($blobData, '\\x') === 0) {
                                        $blobData = hex2bin(substr($blobData, 2));
                                    } elseif (is_resource($blobData)) {
                                        $blobData = stream_get_contents($blobData);
                                    }
                                    $base64Image = base64_encode($blobData);
                                @endphp
                                <img src="data:image/png;base64,{{ $base64Image }}" class="img-fluid rounded" style="max-height: 100%;">
                            @elseif($customer->foto_path)
                                <img src="{{ Storage::url($customer->foto_path) }}" class="img-fluid rounded" style="max-height: 100%;">
                            @else
                                <span class="text-muted">Tidak ada foto</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="col-md-4 text-center">
                        <label class="fw-bold">Kamera (Ambil Foto Baru)</label>
                        <div id="my_camera" class="mx-auto border" style="width: 320px; height: 240px;"></div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-primary" onclick="take_snapshot()">
                                <i class="mdi mdi-camera"></i> Ganti Foto
                            </button>
                        </div>
                        <input type="hidden" name="image" class="image-tag">
                    </div>

                    <div class="col-md-4 text-center">
                        <label class="fw-bold">Preview Foto Baru</label>
                        <div id="results" class="border d-flex align-items-center justify-content-center" style="height: 240px; width: 320px; margin: 0 auto; background: #f8f9fa;">
                            <span class="text-muted">Belum ada foto baru diambil</span>
                        </div>
                    </div>
                </div>

                <div class="mt-5 border-top pt-3 text-end">
                    <a href="{{ route('admin.customer.index') }}" class="btn btn-light">Batal</a>
                    <button type="submit" class="btn btn-warning">Update Data Customer</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        Webcam.set({ width: 320, height: 240, image_format: 'png', jpeg_quality: 90 });
        Webcam.attach('#my_camera');

        function take_snapshot() {
            Webcam.snap(function (data_uri) {
                document.querySelector('.image-tag').value = data_uri;
                document.getElementById('results').innerHTML = `<img src="${data_uri}" class="img-fluid rounded" style="max-height: 100%;"/>`;
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            getKotaAxios({{ $customer->id_kota }});
        });

        function reset(ids) {
            ids.forEach(id => {
                document.getElementById(id).innerHTML = `<option value="">Pilih ${id.charAt(0).toUpperCase() + id.slice(1)}</option>`;
            });
        }

        function getKotaAxios(selectedId = null) {
            const id = document.getElementById('provinsi').value;
            reset(['kota', 'kecamatan', 'kelurahan']);
            if (id) {
                axios.post("{{ route('wilayah.getKota') }}", { id: id, _token: "{{ csrf_token() }}" })
                    .then(res => {
                        let html = '<option value="">Pilih Kota</option>';
                        res.data.data.forEach(item => {
                            let selected = (selectedId == item.id) ? 'selected' : '';
                            html += `<option value="${item.id}" ${selected}>${item.name}</option>`;
                        });
                        document.getElementById('kota').innerHTML = html;
                        if(selectedId) getKecamatanAxios({{ $customer->id_kecamatan }});
                    });
            }
        }

        function getKecamatanAxios(selectedId = null) {
            const id = document.getElementById('kota').value;
            reset(['kecamatan', 'kelurahan']);
            if (id) {
                axios.post("{{ route('wilayah.getKecamatan') }}", { id: id, _token: "{{ csrf_token() }}" })
                    .then(res => {
                        let html = '<option value="">Pilih Kecamatan</option>';
                        res.data.data.forEach(item => {
                            let selected = (selectedId == item.id) ? 'selected' : '';
                            html += `<option value="${item.id}" ${selected}>${item.name}</option>`;
                        });
                        document.getElementById('kecamatan').innerHTML = html;
                        if(selectedId) getKelurahanAxios({{ $customer->id_kelurahan }});
                    });
            }
        }

        function getKelurahanAxios(selectedId = null) {
            const id = document.getElementById('kecamatan').value;
            reset(['kelurahan']);
            if (id) {
                axios.post("{{ route('wilayah.getKelurahan') }}", { id: id, _token: "{{ csrf_token() }}" })
                    .then(res => {
                        let html = '<option value="">Pilih Kelurahan</option>';
                        res.data.data.forEach(item => {
                            let selected = (selectedId == item.id) ? 'selected' : '';
                            html += `<option value="${item.id}" ${selected}>${item.name}</option>`;
                        });
                        document.getElementById('kelurahan').innerHTML = html;
                    });
            }
        }

        document.getElementById('editForm').addEventListener('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                this.reportValidity();
                return;
            }
        });
    </script>
@endsection