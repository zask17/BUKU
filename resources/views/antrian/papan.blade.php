<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Monitor Layar Utama Papan Antrian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6fb;
            font-family: system-ui, -apple-system, sans-serif;
        }

        .papan-panggilan-utama {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: #fff;
            border-radius: 20px;
        }

        .nomor-jumbo {
            font-size: 7.5rem;
            font-weight: 900;
        }

        .card-sub-antrian {
            border-radius: 15px;
            background: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, .05);
            border: 1px solid #e2e8f0;
        }
    </style>
</head>

<body>
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="fw-bold text-dark mb-1">MONITOR PUBLIC DISPLAY ANTRIAN</h2>
                <p class="text-muted fw-semibold" id="liveClock">Memuat Waktu Server...</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div
                    class="card papan-panggilan-utama p-4 text-center border-0 shadow h-100 d-flex align-items-center justify-content-center">
                    <div class="card-body w-100 py-4">
                        <h4 class="fw-bold text-white-50 text-uppercase mb-2 tracking-wide">SEDANG DIPANGGIL</h4>
                        <hr class="border-white-50 my-3">
                        <div class="nomor-jumbo my-3 text-warning" id="papanNomor">-</div>
                        <h2 class="text-white fw-bold text-capitalize" id="papanNama">Antrian Kosong</h2>
                        <h4 class="text-info fw-bold text-uppercase mt-2" id="papanPoli">-</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                    <div class="card-body p-4">
                        <h4 class="text-primary fw-bold mb-4">Urutan Antrian Berikutnya</h4>
                        <div class="row g-3" id="papanGridTunggu">
                            <div class="col-12 text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="text-muted mt-2">Menghubungkan ke layanan live data stream...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let lastCalledId = null;
        let sseReconnectDelay = 1000;
        let audioDingdong = new Audio("{{ asset('audio/dingdong.mp3') }}");

        // Jam Digital Monitor
        setInterval(() => {
            const now = new Date();
            document.getElementById('liveClock').innerText = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) + ' - ' + now.toLocaleTimeString('id-ID') + ' WIB';
        }, 1000);

        // ===================== SSE STREAM CLIENT =====================
        function initSSE() {
            if (!('EventSource' in window)) {
                document.getElementById('papanGridTunggu').innerHTML = '<div class="col-12 text-center text-danger">Browser tidak mendukung SSE.</div>';
                return;
            }

            const source = new EventSource("{{ route('antrian.stream') }}");

            source.addEventListener('queue-update', function (e) {
                const data = JSON.parse(e.data);

                sseReconnectDelay = 1000;

                const active = data.sedang_dipanggil || null;

                if (active && active.idantrian) {
                    document.getElementById('papanNomor').innerText = active.nomor || '-';
                    document.getElementById('papanNama').innerText = active.nama || 'Antrian Kosong';
                    document.getElementById('papanPoli').innerText = active.nama_poli || '-';

                    if (lastCalledId !== active.idantrian) {
                        lastCalledId = active.idantrian;
                        bunyiSuaraPanggilan(active.nomor, active.nama, active.nama_poli);
                    }
                } else {
                    document.getElementById('papanNomor').innerText = "-";
                    document.getElementById('papanNama').innerText = "Antrian Kosong";
                    document.getElementById('papanPoli').innerText = "-";
                    lastCalledId = null;
                }

                let htmlGrid = '';
                const antrianTunggu = data.daftar_tunggu ? data.daftar_tunggu.slice(0, 4) : [];

                antrianTunggu.forEach(item => {
                    htmlGrid += `
            <div class="col-6">
                <div class="card card-sub-antrian p-3 text-center">
                    <h3 class="text-primary class-nomor fw-bold mb-1">${item.nomor}</h3>
                    <h6 class="text-dark fw-bold text-truncate mb-1 text-capitalize">${item.nama}</h6>
                    <span class="badge bg-light text-secondary border rounded-pill small">${item.nama_poli}</span>
                </div>
            </div>`;
                });

                document.getElementById('papanGridTunggu').innerHTML = htmlGrid || `
        <div class="col-12 text-center py-5 text-muted">
            <h5 class="fw-bold">Semua Pasien Telah Dilayani</h5>
            <p class="small mb-0">Tidak ada antrian tunggu tersisa saat ini.</p>
        </div>`;
            });

            source.onerror = function () {
                console.error('Koneksi SSE terputus. Reconnect dalam ' + sseReconnectDelay + 'ms...');
                source.close();
                setTimeout(initSSE, sseReconnectDelay);
                sseReconnectDelay = Math.min(sseReconnectDelay * 2, 30000);
            };
        }

        document.addEventListener('DOMContentLoaded', initSSE);

        // ===================== PANGGILAN SUARA (Dingdong + Speech) =====================
        // Panduan dari: Web Speech API + audio MP3
        function bunyiSuaraPanggilan(nomor, nama, poli) {
            // Batalkan speech yang sedang berjalan
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
            }

            // 1. Mainkan suara dingdong terlebih dahulu
            if (audioDingdong) {
                audioDingdong.currentTime = 0;
                audioDingdong.play().catch(err => console.warn('Autoplay dingdong diblokir:', err));
            }

            // 2. Setelah audio selesai, ucapkan teks panggilan
            audioDingdong.onended = function() {
                if (!('speechSynthesis' in window)) return;

                const kalimat = `Nomor antrian ${nomor}. Atas nama ${nama}. Silakan menuju ke ${poli}.`;
                const utterance = new SpeechSynthesisUtterance(kalimat);
                utterance.lang = 'id-ID';
                utterance.rate = 0.85;
                utterance.pitch = 1.0;
                utterance.volume = 1.0;
                window.speechSynthesis.speak(utterance);
            };

            // Fallback jika audio gagal dimuat: langsung speech
            if (!audioDingdong || audioDingdong.error) {
                audioDingdong.onended = null;
                if ('speechSynthesis' in window) {
                    const kalimat = `Nomor antrian ${nomor}. Atas nama ${nama}. Silakan menuju ke ${poli}.`;
                    const utterance = new SpeechSynthesisUtterance(kalimat);
                    utterance.lang = 'id-ID';
                    utterance.rate = 0.85;
                    utterance.pitch = 1.0;
                    window.speechSynthesis.speak(utterance);
                }
            }
        }
    </script>
</body>

</html>