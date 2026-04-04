@extends($layout)

@section('title-page')
    <span class="page-title-icon bg-gradient-primary text-white me-2">
        <i class="mdi mdi-map-marker"></i>
    </span> Manajemen Wilayah - Axios
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">Wilayah (Axios)</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-gradient-primary text-white">
        Wilayah Administrasi Indonesia (Axios & API)
    </div>

    <div class="card-body">

        {{-- SELECT (SAMA SEPERTI AJAX) --}}
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Provinsi</label>
                <select id="provinsi" class="form-control" onchange="getKotaAxios()">
                    <option value="0">Pilih Provinsi</option>
                    @foreach($provinsis as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Kota</label>
                <select id="kota" class="form-control" onchange="getKecamatanAxios()">
                    <option value="0">Pilih Kota</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Kecamatan</label>
                <select id="kecamatan" class="form-control" onchange="getKelurahanAxios()">
                    <option value="0">Pilih Kecamatan</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Kelurahan</label>
                <select id="kelurahan" class="form-control" onchange="updateLokasiTerpilih()">
                    <option value="0">Pilih Kelurahan</option>
                </select>
            </div>
        </div>

        {{-- LOKASI TERPILIH (SAMA SEPERTI AJAX) --}}
        <div id="lokasi-box" class="mt-4 p-3 rounded border-start border-primary border-4"
             style="background: #f8f9fa; display: none;">
            <p class="text-muted small font-weight-bold text-uppercase mb-1">
                <i class="mdi mdi-map-marker-check me-1 text-primary"></i>
                Lokasi Terpilih (Hasil Axios):
            </p>
            <p id="lokasi-terpilih"
               class="mb-0 font-weight-bold text-primary"
               style="font-size: 1rem; letter-spacing: 0.5px;">
            </p>
        </div>

    </div>
</div>

{{-- AXIOS --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    function reset(ids) {
        ids.forEach(id => {
            const label = id.charAt(0).toUpperCase() + id.slice(1);
            document.getElementById(id).innerHTML =
                `<option value="0">Pilih ${label}</option>`;
        });
        updateLokasiTerpilih();
    }

    function updateLokasiTerpilih() {
        const parts = ['kelurahan', 'kecamatan', 'kota', 'provinsi']
            .map(id => {
                const sel = document.getElementById(id);
                return sel.options[sel.selectedIndex]?.value != '0'
                    ? sel.options[sel.selectedIndex].text
                    : null;
            })
            .filter(Boolean);

        const box = document.getElementById('lokasi-box');

        if (parts.length > 0) {
            document.getElementById('lokasi-terpilih').textContent = parts.join(', ');
            box.style.display = 'block';
        } else {
            box.style.display = 'none';
        }
    }

    function getKotaAxios() {
        const id = document.getElementById('provinsi').value;
        reset(['kota', 'kecamatan', 'kelurahan']);

        if (id != 0) {
            axios.post("{{ route('wilayah.getKota') }}", {
                id: id,
                _token: "{{ csrf_token() }}"
            })
            .then(res => {
                res.data.data.forEach(item => {
                    document.getElementById('kota').innerHTML +=
                        `<option value="${item.id}">${item.name}</option>`;
                });
            });
        }

        updateLokasiTerpilih();
    }

    function getKecamatanAxios() {
        const id = document.getElementById('kota').value;
        reset(['kecamatan', 'kelurahan']);

        if (id != 0) {
            axios.post("{{ route('wilayah.getKecamatan') }}", {
                id: id,
                _token: "{{ csrf_token() }}"
            })
            .then(res => {
                res.data.data.forEach(item => {
                    document.getElementById('kecamatan').innerHTML +=
                        `<option value="${item.id}">${item.name}</option>`;
                });
            });
        }

        updateLokasiTerpilih();
    }

    function getKelurahanAxios() {
        const id = document.getElementById('kecamatan').value;
        reset(['kelurahan']);

        if (id != 0) {
            axios.post("{{ route('wilayah.getKelurahan') }}", {
                id: id,
                _token: "{{ csrf_token() }}"
            })
            .then(res => {
                res.data.data.forEach(item => {
                    document.getElementById('kelurahan').innerHTML +=
                        `<option value="${item.id}">${item.name}</option>`;
                });
            });
        }

        updateLokasiTerpilih();
    }
</script>
@endsection