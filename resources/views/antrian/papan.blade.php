<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Papan Antrian - Monitor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{background:#f4f6fb;margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,'Helvetica Neue',Arial}
        .papan-panggilan-utama{background:linear-gradient(135deg,#1e3c72 0%,#2a5298 100%);color:#fff;border-radius:20px}
        .nomor-jumbo{font-size:8rem;font-weight:900;letter-spacing:-2px}
        .card-sub-poli{border:none;border-radius:15px;transition:transform .2s;background:#fff;box-shadow:0 4px 6px rgba(0,0,0,.05)}
        .live-clock{color:#eef2ff}
    </style>
</head>
<body>
    <div class="container-fluid py-3">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="fw-bold text-dark mb-1">MONITOR ANTRIAN PASIEN</h2>
                <p class="text-muted mb-0 live-clock" id="liveClock">Memuat waktu server...</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card papan-panggilan-utama p-4 text-center shadow border-0 h-100 d-flex align-items-center justify-content-center">
                    <div class="card-body w-100 py-5">
                        <h4 class="fw-bold tracking-wide text-white-50 text-uppercase mb-3">SEDANG DIPANGGIL</h4>
                        <hr class="border-white-50 my-3">
                        <div class="nomor-jumbo my-4 text-warning" id="papanNomor">-</div>
                        <h2 class="text-white fw-bold text-capitalize px-2 mb-2" id="papanNama">Silakan Ambil Antrian</h2>
                        <h4 class="text-info fw-semibold mb-0 text-uppercase" id="papanPoli">-</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                    <div class="card-body p-4">
                        <h4 class="text-primary fw-bold mb-4 d-flex align-items-center">
                            <span>Urutan Antrian Berikutnya</span>
                        </h4>

                        <div class="row g-3" id="papanGridTunggu">
                            <div class="col-12 text-center py-5 text-muted">
                                <div class="spinner-border text-primary mb-3" role="status"></div>
                                <p class="mb-0">Menghubungkan ke server antrian real-time...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let lastCalledId = null;

        // Jam Digital Real-Time
        setInterval(() => {
            const now = new Date();
            const live = document.getElementById('liveClock');
            if(live) live.innerText = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) + ' - ' + now.toLocaleTimeString('id-ID') + ' WIB';
        }, 1000);

        // SSE Connection
        if (!!window.EventSource) {
            const source = new EventSource("{{ route('antrian.stream') }}");

            source.addEventListener('queue-update', function (e) {
                const data = JSON.parse(e.data);

                if (data.sedang_dipanggil) {
                    document.getElementById('papanNomor').innerText = data.sedang_dipanggil.nomor;
                    document.getElementById('papanNama').innerText = data.sedang_dipanggil.nama;
                    document.getElementById('papanPoli').innerText = data.sedang_dipanggil.nama_poli;

                    if (lastCalledId !== data.sedang_dipanggil.idantrian) {
                        lastCalledId = data.sedang_dipanggil.idantrian;
                        bunyiSuaraPanggilan(data.sedang_dipanggil.nomor, data.sedang_dipanggil.nama, data.sedang_dipanggil.nama_poli);
                    }
                } else {
                    document.getElementById('papanNomor').innerText = "-";
                    document.getElementById('papanNama').innerText = "Silakan Ambil Antrian";
                    document.getElementById('papanPoli').innerText = "-";
                    lastCalledId = null;
                }

                let htmlGrid = '';
                const limitTunggu = data.daftar_tunggu.slice(0, 4);
                limitTunggu.forEach(item => {
                    htmlGrid += `
                        <div class="col-6">
                            <div class="card card-sub-poli p-4 text-center border">
                                <h2 class="text-primary fw-bold mb-1">${item.nomor}</h2>
                                <h5 class="text-dark fw-bold text-truncate mb-1 text-capitalize">${item.nama}</h5>
                                <span class="badge bg-light text-secondary border rounded-pill small">${item.nama_poli}</span>
                            </div>
                        </div>`;
                });

                document.getElementById('papanGridTunggu').innerHTML = htmlGrid || `
                    <div class="col-12 text-center py-5 text-muted">
                        <h5 class="fw-bold">Semua Pasien Telah Dilayani</h5>
                        <p class="small mb-0">Antrian tunggu saat ini kosong.</p>
                    </div>`;
            });

            source.onerror = function(err) { console.error('SSE error:', err); };
        } else {
            document.getElementById('papanGridTunggu').innerHTML = '<div class="col-12 text-center text-danger py-4">Browser tidak mendukung SSE.</div>';
        }

        function bunyiSuaraPanggilan(nomor, nama, poli) {
            if (!('speechSynthesis' in window)) return;
            window.speechSynthesis.cancel();
            const kalimat = `Nomor antrian, ${nomor}. Atas nama, ${nama}. Silakan menuju ke, ${poli}.`;
            const u = new SpeechSynthesisUtterance(kalimat);
            u.lang = 'id-ID'; u.rate = 0.85; u.pitch = 1.0; u.volume = 1.0;
            window.speechSynthesis.speak(u);
        }
    </script>
</body>
</html>