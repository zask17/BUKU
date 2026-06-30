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

        /* Overlay aktivasi suara */
        #soundOverlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(0, 0, 0, 0.75);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.5s ease;
        }
        #soundOverlay.activated {
            opacity: 0;
            pointer-events: none;
        }
        .sound-card {
            background: #fff;
            border-radius: 24px;
            padding: 3rem 4rem;
            max-width: 500px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        .sound-card .icon-speaker {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .sound-card h3 {
            font-weight: 800;
            color: #1e3c72;
        }
        .sound-card p {
            color: #666;
            margin-bottom: 1.5rem;
        }
        .btn-aktifkan {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 14px 40px;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .btn-aktifkan:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(30, 60, 114, 0.4);
        }
        .btn-aktifkan:active {
            transform: scale(0.97);
        }
        .sound-status-aktif {
            position: fixed;
            top: 10px;
            right: 15px;
            z-index: 9998;
            background: #28a745;
            color: #fff;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            box-shadow: 0 2px 10px rgba(40, 167, 69, 0.3);
            opacity: 0;
            transition: opacity 0.5s;
            pointer-events: none;
        }
        .sound-status-aktif.visible {
            opacity: 1;
        }
    </style>
</head>

<body>
    <!-- Overlay aktivasi suara (harus diklik sekali biar browser izinkan autoplay) -->
    <div id="soundOverlay">
        <div class="sound-card">
            <div class="icon-speaker">🔊</div>
            <h3>Aktifkan Suara Papan</h3>
            <p>Klik tombol di bawah untuk mengaktifkan suara panggilan antrian.<br>Browser memerlukan interaksi pengguna satu kali.</p>
            <button class="btn-aktifkan" onclick="aktifkanSuara()">🔊 Aktifkan Suara</button>
        </div>
    </div>
    <div id="soundStatus" class="sound-status-aktif">🔊 Suara Aktif</div>

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
        let suaraAktif = false;
        let audioHospital = new Audio("{{ asset('audio/hospital-announcement.mpeg') }}");

        // ===================== AKTIVASI SUARA (User Gesture) =====================
        // Browser blokir autoplay audio & speech tanpa interaksi pengguna.
        // Overlay ini memastikan user mengklik sekali saat tab pertama dibuka.
        function aktifkanSuara() {
            if (suaraAktif) return;

            // 1. Putar audio senyap sebentar untuk unlock AudioContext
            if (audioHospital) {
                audioHospital.volume = 0.01;
                audioHospital.currentTime = 0;
                audioHospital.play().then(function () {
                    audioHospital.pause();
                    audioHospital.volume = 1.0;
                }).catch(function () {});
            }

            // 2. Unlock SpeechSynthesis dengan ucapan singkat
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
                var u = new SpeechSynthesisUtterance(' ');
                u.volume = 0;
                u.rate = 10;
                window.speechSynthesis.speak(u);
            }

            suaraAktif = true;

            // Sembunyikan overlay
            document.getElementById('soundOverlay').classList.add('activated');
            document.getElementById('soundStatus').classList.add('visible');

            // Jika sudah ada antrian yang ditampilkan, langsung suarakan
            var nomorEl = document.getElementById('papanNomor').innerText;
            var namaEl = document.getElementById('papanNama').innerText;
            var poliEl = document.getElementById('papanPoli').innerText;
            if (nomorEl && nomorEl !== '-' && namaEl && namaEl !== 'Antrian Kosong') {
                bunyiSuaraPanggilan(nomorEl, namaEl, poliEl);
            }
        }

        // Jam Digital Monitor
        setInterval(() => {
            const now = new Date();
            document.getElementById('liveClock').innerText = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) + ' - ' + now.toLocaleTimeString('id-ID') + ' WIB';
        }, 1000);

        // ===================== POLLING DATA ANTRIAN (3 DETIK) =====================
        // Menggunakan polling sebagai pengganti SSE karena php artisan serve
        // single-threaded — koneksi SSE permanen memblokir request tab lain.
        async function ambilDataPapan() {
            try {
                const res = await fetch("{{ route('antrian.papan.data') }}");
                const data = await res.json();

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
            } catch (err) {
                console.error('Gagal polling data antrian:', err);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            ambilDataPapan(); // Langsung ambil saat pertama kali buka
            setInterval(ambilDataPapan, 3000); // Ulang tiap 3 detik
        });

        // ===================== PANGGILAN SUARA (Hospital Announcement + Speech) =====================
        // Panduan dari: Web Speech API + audio MPEG
        // NOTE: pakai callback chain + safety timeout karena SpeechSynthesis di Chrome
        // sering buggy dengan async/await (onend tidak selalu terpanggil).
        function bicaraBertahap(teks, cb, delayMs) {
            const u = new SpeechSynthesisUtterance(teks);
            u.lang = 'id-ID';
            u.rate = 0.85;
            u.pitch = 1.0;
            u.volume = 1.0;

            var called = false; // Mencegah dobel panggil dari safety timeout + onend
            var amanPanggil = function () {
                if (called) return;
                called = true;
                if (delayMs != null) {
                    setTimeout(cb, delayMs);
                } else if (cb) {
                    cb();
                }
            };

            u.onend = amanPanggil;
            u.onerror = amanPanggil;
            window.speechSynthesis.speak(u);

            // Safety timeout: jika onend/onerror tidak pernah terpanggil (bug Chrome),
            // paksa lanjut setelah durasi estimasi
            if (delayMs != null) {
                var estimated = Math.max(teks.length * 60, 800) + delayMs;
                setTimeout(amanPanggil, estimated);
            }
        }

        function ucapkanPanggilan(nomor, nama, poli) {
            if (!('speechSynthesis' in window)) return;

            // Urutan:
            //   "Nomor antrian"  → [delay 1dtk]  →  "{nomor}"
            //   → [delay 300ms]  →  "Atas nama {nama}. Silakan menuju ke {poli}."
            bicaraBertahap('Nomor antrian', function () {
                bicaraBertahap(nomor, function () {
                    bicaraBertahap('Atas nama ' + nama + '. Silakan menuju ke ' + poli + '.');
                }, 300);
            }, 300);
        }

        function bunyiSuaraPanggilan(nomor, nama, poli) {
            if (!suaraAktif) return; // Belum diaktifkan user
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
            }

            // 1. Mainkan suara hospital announcement
            if (audioHospital) {
                audioHospital.currentTime = 0;
                audioHospital.play().catch(err => console.warn('Autoplay hospital announcement diblokir:', err));
            }

            // 2. Setelah audio selesai → TTS bertahap
            var siapUcap = function () {
                ucapkanPanggilan(nomor, nama, poli);
            };

            if (audioHospital && !audioHospital.error) {
                audioHospital.onended = siapUcap;
            } else {
                // Fallback: langsung TTS jika audio gagal
                audioHospital.onended = null;
                siapUcap();
            }
        }
    </script>
</body>

</html>