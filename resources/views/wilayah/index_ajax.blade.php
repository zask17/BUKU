@extends($layout)

@section('title-page')
    <span class="page-title-icon bg-gradient-info text-white me-2">
        <i class="mdi mdi-map-marker"></i>
    </span> Manajemen Wilayah - Konsep AJAX
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Wilayah (AJAX)</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-gradient-info text-white">
        Wilayah Administrasi Indonesia (JQuery AJAX &amp; API)
    </div>
    <div class="card-body">

        {{-- Select Row --}}
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Provinsi</label>
                <select id="provinsi" class="form-control" onchange="getKotaAjax()">
                    <option value="0">Pilih Provinsi</option>
                    @foreach($provinsis as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Kota</label>
                <select id="kota" class="form-control" onchange="getKecamatanAjax()">
                    <option value="0">Pilih Kota</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Kecamatan</label>
                <select id="kecamatan" class="form-control" onchange="getKelurahanAjax()">
                    <option value="0">Pilih Kecamatan</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Kelurahan</label>
                <select id="kelurahan" class="form-control" onchange="updateLabel('kelurahan')">
                    <option value="0">Pilih Kelurahan</option>
                </select>
            </div>
        </div>

        {{-- Lokasi Terpilih --}}
        <div id="lokasi-box" class="mt-4 p-3 rounded border-start border-info border-4" style="background: #f8f9fa; display: none;">
            <p class="text-muted small font-weight-bold text-uppercase mb-1">
                <i class="mdi mdi-map-marker-check me-1 text-info"></i> Lokasi Terpilih (Hasil Ajax):
            </p>
            <p id="lokasi-terpilih" class="mb-0 font-weight-bold text-info" style="font-size: 1rem; letter-spacing: 0.5px;"></p>
        </div>

    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    function resetSelect(ids) {
        ids.forEach(id => {
            let label = id.charAt(0).toUpperCase() + id.slice(1);
            $(`#${id}`).html(`<option value="0">Pilih ${label}</option>`);
        });
        updateLokasiTerpilih();
    }

    function updateLabel(id) {
        updateLokasiTerpilih();
    }

    function updateLokasiTerpilih() {
        const parts = ['kelurahan', 'kecamatan', 'kota', 'provinsi'].map(id => {
            const sel = document.getElementById(id);
            return sel.options[sel.selectedIndex]?.value != '0' ? sel.options[sel.selectedIndex].text : null;
        }).filter(Boolean);

        if (parts.length > 0) {
            $('#lokasi-terpilih').text(parts.join(', '));
            $('#lokasi-box').show();
        } else {
            $('#lokasi-box').hide();
        }
    }

    function getKotaAjax() {
        let id = $('#provinsi').val();
        resetSelect(['kota', 'kecamatan', 'kelurahan']);
        updateLokasiTerpilih();
        if (id != 0) {
            $.post("{{ route('wilayah.getKota') }}", { id: id, _token: "{{ csrf_token() }}" }, function(res) {
                res.data.forEach(item => $('#kota').append(`<option value="${item.id}">${item.name}</option>`));
            });
        }
    }

    function getKecamatanAjax() {
        let id = $('#kota').val();
        resetSelect(['kecamatan', 'kelurahan']);
        updateLokasiTerpilih();
        if (id != 0) {
            $.post("{{ route('wilayah.getKecamatan') }}", { id: id, _token: "{{ csrf_token() }}" }, function(res) {
                res.data.forEach(item => $('#kecamatan').append(`<option value="${item.id}">${item.name}</option>`));
            });
        }
    }

    function getKelurahanAjax() {
        let id = $('#kecamatan').val();
        resetSelect(['kelurahan']);
        updateLokasiTerpilih();
        if (id != 0) {
            $.post("{{ route('wilayah.getKelurahan') }}", { id: id, _token: "{{ csrf_token() }}" }, function(res) {
                res.data.forEach(item => $('#kelurahan').append(`<option value="${item.id}">${item.name}</option>`));
            });
        }
    }
</script>
@endsection