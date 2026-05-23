@extends('layouts.admin.main')

@section('title-page', 'Panel Operator Antrian')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Monitor Antrian</li>
@endsection

@section('style-page')
    <style>
        .display-antrian-utama {
            font-size: 6rem;
            font-weight: bold;
            color: #fe7c96;
        }

        .scroller-antrian {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
@endsection

@section('content')
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h4 class="card-title mb-0">Manajemen Pemanggilan Ruang Poli</h4>
        </div>
        <div class="col-md-6 d-flex justify-content-md-end">
            <div class="d-flex align-items-center">
                <span class="me-2 fw-bold text-dark">Filter Loket:</span>
                <select id="filterPoli" class="form-select w-auto border-secondary bg-white text-dark">
                    <option value="">-- Semua Poli --</option>
                    @foreach($daftarPoli as $p)
                        <option value="{{ $p->kode_poli }}">{{ $p->nama_poli }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm text-center p-4 bg-white rounded-4">
                <h5 class="text-muted text-uppercase small tracking-wider fw-bold">Sedang Dipanggil</h5>
                <hr class="my-3">
                <div class="display-antrian-utama my-2" id="nomorAktif">-</div>
                <h3 class="text-dark fw-bold text-capitalize px-2 mb-1" id="namaAktif">Tidak Ada Panggilan</h3>
                <p class="text-info fw-semibold mb-4" id="poliAktif">-</p>

                <div class="row g-2">
                    <div class="col-6">
                        <button class="btn btn-gradient-primary w-100 py-3 fw-bold shadow-sm"
                            onclick="panggilUrutanBerikutnya()">
                            <i class="mdi mdi-play-circle me-1"></i> Panggil Berikutnya
                        </button>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-outline-danger w-100 py-3 fw-bold" onclick="lewatkanPasien()">
                            <i class="mdi mdi-skip-next me-1"></i> Lewatkan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-body p-4">
                    <h5 class="text-primary fw-bold mb-3 d-flex justify-content-between">
                        <span>Daftar Tunggu Pasien Hari Ini</span>
                        <span class="badge bg-light text-primary border rounded-pill" id="totalTunggu">0</span>
                    </h5>
                    <div class="scroller-antrian">
                        <ul class="list-group list-group-flush" id="listTunggu">
                            <li class="list-group-item text-center text-muted py-4">Menghubungkan ke server antrian...</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-body p-4">
                    <h5 class="text-danger fw-bold mb-3">Antrian Terlewat <small class="text-muted fs-6">(Double-klik nama untuk panggil ulang)</small></h5>
                    <div class="scroller-antrian">
                        <ul class="list-group list-group-flush" id="listTerlewat">
                            <li class="list-group-item text-center text-muted py-4">Tidak ada antrian terlewat</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script-page')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const csrfToken = "{{ csrf_token() }}";
        let currentActiveIdantrian = null;

        // Membuka Koneksi Persisten Server-Sent Events (SSE) [cite: 15]
        if (!!window.EventSource) { [cite: 15]
            // Mengarah ke nama rute luar baru bebas hambatan auth lock session [cite: 15]
            const source = new EventSource("{{ route('antrian.stream') }}"); [cite: 15]

            // Mendengarkan Event Update dari Server [cite: 25]
            source.addEventListener('queue-update', function (e) { [cite: 25]        
                const data = JSON.parse(e.data); [cite: 25]

                // 1. Update Tampilan Komponen Pasien Aktif
                if (data.sedang_dipanggil) {
                    currentActiveIdantrian = data.sedang_dipanggil.idantrian;
                    document.getElementById('nomorAktif').innerText = data.sedang_dipanggil.nomor;
                    document.getElementById('namaAktif').innerText = data.sedang_dipanggil.nama;
                    document.getElementById('poliAktif').innerText = data.sedang_dipanggil.nama_poli;
                } else {
                    currentActiveIdantrian = null;
                    document.getElementById('nomorAktif').innerText = "-";
                    document.getElementById('namaAktif').innerText = "Tidak Ada Panggilan";
                    document.getElementById('poliAktif').innerText = "-";
                }

                // 2. Render Loop Daftar Antrian Menunggu
                document.getElementById('totalTunggu').innerText = data.daftar_tunggu.length;
                let htmlTunggu = '';
                data.daftar_tunggu.forEach(item => {
                    htmlTunggu += `
                        <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3 bg-light mb-1 border-0 rounded" onclick="panggilSpesifik('${item.idantrian}')" style="cursor:pointer" title="Klik untuk panggil pasien ini">
                            <div>
                                <strong class="text-primary fs-5 me-2">${item.nomor}</strong>
                                <span class="text-dark fw-bold">${item.nama}</span>
                                <br><small class="text-muted">${item.nama_poli}</small>
                            </div>
                            <span class="badge bg-info rounded-pill">Panggil</span>
                        </li>`;
                });
                document.getElementById('listTunggu').innerHTML = htmlTunggu || '<li class="list-group-item text-center text-muted py-4">Antrian tunggu hari ini kosong</li>';

                // 3. Render Loop Daftar Antrian Terlewat
                let htmlTerlewat = '';
                data.terlewat.forEach(item => {
                    htmlTerlewat += `
                        <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3 bg-light mb-1 border-0 rounded" ondblclick="panggilUlangTerlewat('${item.idantrian}')" style="cursor:pointer" title="Double-klik untuk panggil ulang">
                            <div>
                                <b class="text-danger fs-5 me-2">${item.nomor}</b>
                                <span class="text-dark fw-semibold">${item.nama}</span>
                                <br><small class="text-muted">${item.nama_poli}</small>
                            </div>
                            <span class="badge bg-warning text-dark rounded-pill">Terlewat</span>
                        </li>`;
                });
                document.getElementById('listTerlewat').innerHTML = htmlTerlewat || '<li class="list-group-item text-center text-muted py-4">Tidak ada antrian terlewat</li>';
            });

            // Deteksi kegagalan koneksi di background agar tidak blank loading [cite: 25]
            source.onerror = function(err) { [cite: 25]
                console.error("SSE Connection Error:", err); [cite: 25]
                document.getElementById('listTunggu').innerHTML = `
                    <li class="list-group-item list-group-item-danger text-center py-4 border-0 rounded">
                        <i class="mdi mdi-alert-circle me-1"></i> Gagal terhubung ke data real-time. Mencoba menyambung ulang...
                    </li>`;
            }; [cite: 25]
        } else {
            document.getElementById('listTunggu').innerHTML = '<li class="list-group-item list-group-item-danger text-center py-4">Browser tidak mendukung SSE.</li>';
        }

        // ===================== FUNGSI AXIOS HTTP REQUEST ===================== [cite: 26]

        function panggilUrutanBerikutnya() {
            const kp = document.getElementById('filterPoli').value;
            axios.post("{{ route('admin.antrian.panggil') }}", { kode_poli: kp }, { headers: { 'X-CSRF-TOKEN': csrfToken } })
                .catch(err => alert(err.response?.data?.message || 'Gagal memanggil antrian.'));
        }

        function lewatkanPasien() {
            if (!currentActiveIdantrian) {
                alert('Tidak ada antrian aktif yang sedang dipanggil.');
                return;
            }
            axios.post("{{ route('admin.antrian.lewatkan') }}", { idantrian: currentActiveIdantrian }, { headers: { 'X-CSRF-TOKEN': csrfToken } })
                .catch(err => alert(err.response?.data?.message || 'Gagal melewatkan pasien.'));
        }

        function panggilSpesifik(id) {
            axios.post("{{ route('admin.antrian.panggil') }}", { idantrian: id }, { headers: { 'X-CSRF-TOKEN': csrfToken } })
                .catch(err => alert(err.response?.data?.message || 'Gagal memanggil pasien terpilih.'));
        }

        function panggilUlangTerlewat(id) {
            axios.post("{{ route('admin.antrian.panggil_terlewat') }}", { idantrian: id }, { headers: { 'X-CSRF-TOKEN': csrfToken } })
                .catch(err => alert(err.response?.data?.message || 'Gagal memanggil ulang pasien terlewat.'));
        }
    </script>
@endsection