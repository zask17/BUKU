@extends('layouts.admin.main')

@section('title-page', 'Manajemen Loket Antrian')
@section('breadcrumb')
    <li class=\"breadcrumb-item active\" aria-current=\"page\">Antrian</li>
@endsection

@section('style-page')
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.0.0/css/all.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow border-0">
            <div class="card-body bg-light d-flex align-items-center justify-content-between">
                <h5 class="mb-0 text-dark font-weight-bold">Pilih Poli yang Dikelola:</h5>
                <form action="{{ route('admin.antrian.index') }}" method="GET" class="d-flex w-50">
                    <select name="idpoli" class="form-select form-select-lg me-2" onchange="this.form.submit()">
                        @foreach($polis as $p)
                            <option value="{{ $p->idpoli }}" {{ $selectedPoliId == $p->idpoli ? 'selected' : '' }}>
                                {{ $p->nama_poli }} ({{ $p->kode_poli }})
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card shadow border-0 text-center mb-4">
            <div class="card-header bg-dark text-white py-3">
                <h5 class="mb-0">Antrian yang Sedang Dipanggil</h5>
            </div>
            <div class="card-body py-4">
                <h1 class="display-2 font-weight-bold text-success mb-2" id=\"current-number\">---</h1>
                <h4 class="text-muted mb-4" id=\"current-name\">Tidak Ada Pasien</h4>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <button class="btn btn-success btn-lg px-4 py-3" id=\"btn-panggil-next\">
                        <i class="fas fa-volume-up me-2\"></i>Panggil Berikutnya
                    </button>
                    <button class="btn btn-danger btn-lg px-4 py-3" id=\"btn-lewatkan\">
                        <i class="fas fa-forward me-2\"></i>Lewatkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7 mb-4">
        <div class="card shadow border-0 mb-4">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Tunggu Antrian</h5>
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No. Urut</th>
                                <th>Nama Pasien</th>
                                <th>Jam Masuk</th>
                            </tr>
                        </thead>
                        <tbody id=\"queue-table-body\">
                            <tr>
                                <td colspan="3" class="text-center text-muted">Memuat data antrian...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow border-0">
            <div class="card-header bg-warning text-dark py-3">
                <h5 class="mb-0"><i class="fas fa-user-clock me-2"></i>Antrian Terlewat / Skipped</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pasien</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id=\"skipped-table-body\">
                            <tr>
                                <td colspan="3" class="text-center text-muted">Tidak ada antrian terlewat</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = "{{ csrf_token() }}";
    const selectedPoliId = "{{ $selectedPoliId }}";

    // Hubungkan EventSource ke stream dengan mengirimkan parameter idpoli aktif
    const source = new EventSource("{{ route('admin.antrian.stream') }}?idpoli=" + selectedPoliId);

    source.addEventListener('queue-update', function(e) {
        const data = JSON.parse(e.data);
        
        // Render Panggilan Aktif
        if (data.antrian_sekarang) {
            document.getElementById('current-number').innerText = String(data.antrian_sekarang.nomor).padStart(3, '0');
            document.getElementById('current-name').innerText = data.antrian_sekarang.nama;
        } else {
            document.getElementById('current-number').innerText = "---";
            document.getElementById('current-name').innerText = "Tidak Ada Pasien";
        }

        // Render Daftar Antrian Waiting
        const tbodyList = document.getElementById('queue-table-body');
        tbodyList.innerHTML = '';
        if (data.antrian_list.length === 0) {
            tbodyList.innerHTML = `<tr><td colspan="3" class="text-center text-muted">Antrian Kosong</td></tr>`;
        } else {
            data.antrian_list.forEach(item => {
                const jam = new Date(item.waktu).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
                tbodyList.innerHTML += `<tr>
                    <td><span class="badge bg-primary fs-6">${String(item.nomor).padStart(3, '0')}</span></td>
                    <td><strong>${item.nama}</strong></td>
                    <td>${jam} WIB</td>
                </tr>`;
            });
        }

        // Render Daftar Antrian Skipped
        const tbodySkipped = document.getElementById('skipped-table-body');
        tbodySkipped.innerHTML = '';
        if (data.antrian_terlewat.length === 0) {
            tbodySkipped.innerHTML = `<tr><td colspan="3" class="text-center text-muted">Tidak ada antrian terlewat</td></tr>`;
        } else {
            data.antrian_terlewat.forEach(item => {
                tbodySkipped.innerHTML += `<tr>
                    <td><span class="badge bg-warning text-dark">${String(item.nomor).padStart(3, '0')}</span></td>
                    <td>${item.nama}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-danger" onclick="panggilTerlewat(${item.nomor})">
                            <i class="fas fa-undo me-1"></i>Panggil Ulang
                        </button>
                    </td>
                </tr>`;
            });
        }
    });

    source.onerror = function(err) {
        console.error("SSE Error:", err);
    };

    // Panggil Berikutnya
    document.getElementById('btn-panggil-next').addEventListener('click', function() {
        fetch("{{ route('admin.antrian.panggil') }}", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": csrfToken, "Content-Type": "application/json" },
            body: JSON.stringify({ idpoli: selectedPoliId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'empty') alert(data.message);
        });
    });

    // Lewatkan
    document.getElementById('btn-lewatkan').addEventListener('click', function() {
        fetch("{{ route('admin.antrian.lewatkan') }}", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": csrfToken, "Content-Type": "application/json" },
            body: JSON.stringify({ idpoli: selectedPoliId })
        });
    });

    // Panggil Terlewat
    function panggilTerlewat(nomor) {
        fetch("{{ route('admin.antrian.panggil_terlewat') }}", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": csrfToken, "Content-Type": "application/json" },
            body: JSON.stringify({ nomor: nomor, idpoli: selectedPoliId })
        });
    }
</script>
@endsection