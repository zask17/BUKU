@extends('layouts.admin.main')

@section('title-page', 'Manajemen Loket Antrian')
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Antrian</li>
@endsection

@section('style-page')
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.0.0/css/all.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card shadow border-0 text-center mb-4">
            <div class="card-header bg-dark text-white py-3">
                <h5 class="mb-0">Antrian yang Sedang Dipanggil</h5>
            </div>
            <div class="card-body py-4">
                <h1 class="display-2 font-weight-bold text-success mb-2" id="current-number">---</h1>
                <h4 class="text-muted mb-4" id="current-name">Tidak Ada Pasien</h4>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <button class="btn btn-success btn-lg px-4 py-3" id="btn-panggil-next">
                        <i class="fas fa-volume-up me-2"></i>Panggil Berikutnya
                    </button>
                    <button class="btn btn-warning btn-lg text-white px-4 py-3" id="btn-lewatkan">
                        <i class="fas fa-forward me-2"></i>Lewatkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card shadow border-0 mb-4">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0"><i class="fas fa-list-ol me-2"></i>Daftar Antrian Aktif (Menunggu)</h5>
            </div>
            <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-striped mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>No. Urut</th>
                            <th>Nama Pasien</th>
                            <th>Jam Masuk</th>
                        </tr>
                    </thead>
                    <tbody id="table-waiting-list">
                        <tr><td colspan="3" class="text-center text-muted py-3">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow border-0">
            <div class="card-header bg-danger text-white py-3">
                <h5 class="mb-0"><i class="fas fa-user-times me-2"></i>Daftar Antrian Terlewat</h5>
            </div>
            <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-striped mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>No. Urut</th>
                            <th>Nama Pasien</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="table-skipped-list">
                        <tr><td colspan="3" class="text-center text-muted py-3">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-page')
<script>
    const csrfToken = "{{ csrf_token() }}";

    const source = new EventSource("{{ route('antrian.stream') }}");

    source.addEventListener('queue-update', function(event) {
        const data = JSON.parse(event.data);

        // Update Antrian Sedang Dipanggil
        if (data.antrian_sekarang) {
            document.getElementById('current-number').innerText = String(data.antrian_sekarang.nomor).padStart(3, '0');
            document.getElementById('current-name').innerText = data.antrian_sekarang.nama;
        } else {
            document.getElementById('current-number').innerText = "---";
            document.getElementById('current-name').innerText = "Tidak Ada Pasien";
        }

        // Waiting List
        const waitingBody = document.getElementById('table-waiting-list');
        waitingBody.innerHTML = '';
        if (!data.antrian_list || data.antrian_list.length === 0) {
            waitingBody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">Tidak ada antrian menunggu.</td></tr>';
        } else {
            data.antrian_list.forEach(item => {
                const waktu = item.waktu ? new Date(item.waktu).toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'}) : '-';
                waitingBody.innerHTML += `
                    <tr>
                        <td><strong>${String(item.nomor).padStart(3, '0')}</strong></td>
                        <td>${item.nama}</td>
                        <td>${waktu}</td>
                    </tr>`;
            });
        }

        // Skipped List
        const skippedBody = document.getElementById('table-skipped-list');
        skippedBody.innerHTML = '';
        if (!data.antrian_terlewat || data.antrian_terlewat.length === 0) {
            skippedBody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">Tidak ada antrian terlewat.</td></tr>';
        } else {
            data.antrian_terlewat.forEach(item => {
                skippedBody.innerHTML += `
                    <tr>
                        <td><strong>${String(item.nomor).padStart(3, '0')}</strong></td>
                        <td>${item.nama}</td>
                        <td class="text-center">
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
            headers: { "X-CSRF-TOKEN": csrfToken, "Content-Type": "application/json" }
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
            headers: { "X-CSRF-TOKEN": csrfToken, "Content-Type": "application/json" }
        });
    });

    // Panggil Terlewat
    function panggilTerlewat(nomor) {
        fetch("{{ route('admin.antrian.panggil_terlewat') }}", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": csrfToken, "Content-Type": "application/json" },
            body: JSON.stringify({ nomor: nomor })
        });
    }
</script>
@endsection