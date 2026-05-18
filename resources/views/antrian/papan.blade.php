<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layar Utama Papan Antrian Publik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/all.min.css" rel="stylesheet">
    <style>
        .display-box { background: linear-gradient(135deg, #1e3c72, #2a5298); color: white; border-radius: 15px; }
        .footer-marquee { background-color: #f8f9fa; border-top: 4px solid #1e3c72; }
    </style>
</head>
<body class="bg-light" style="overflow: hidden;">
    <div id="gesture-overlay" class="w-100 bg-warning text-dark text-center py-2 font-weight-bold" style="position: fixed; top:0; z-index:9999;">
        <button class="btn btn-sm btn-dark me-2" onclick="initAudioGesture()"><i class="fas fa-volume-up me-1"></i>Aktifkan Audio Monitor</button>
        Harap klik tombol ini terlebih dahulu agar suara notifikasi panggilan otomatis dapat diaktifkan. 
    </div>

    <div class="container-fluid min-vh-100 d-flex flex-column justify-content-between py-5 mt-3">
        <div class="text-center mb-2">
            <h1 class="display-4 fw-bold text-uppercase tracking-wide text-dark">Papan Informasi Antrian Utama</h1>
            <p class="lead text-muted" id="live-clock">Minggu, 17 Mei 2026 | 00:00:00</p>
        </div>

        <div class="row px-4 my-auto align-items-center justify-content-center">
            <div class="col-lg-7 text-center p-3">
                <div class="display-box shadow p-5">
                    <h2 class="text-uppercase tracking-wider opacity-75 fw-semibold mb-3">NOMOR ANTRIAN DIPANGGIL</h2>
                    <hr class="border-light opacity-25 w-50 mx-auto">
                    <h1 class="display-1 fw-bold my-4 font-monospace" id="papan-nomor" style="font-size: 8rem;">000</h1>
                    <div class="bg-white text-dark rounded-pill py-2 px-4 d-inline-block shadow-sm">
                        <h4 class="mb-0 fw-bold" id="papan-nama">Menunggu Operator...</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-marquee p-3 fixed-bottom shadow-lg">
            <marquee behavior="scroll" direction="left" class="fs-4 text-primary fw-medium">
                Selamat Datang di Fasilitas Layanan Kesehatan Mandiri — Budayakan Mengantri Demi Kenyamanan Bersama — Silakan Ambil Tiket di Kios Mesin Antrian Guest Mandiri.
            </marquee>
        </div>
    </div>

    <audio src="https://assets.mixkit.co/active_storage/sfx/2869/2869-84.wav" id="audio-tingtong" preload="auto"></audio>

    <script>
        let audioAllowed = false;
        let lastTimestamp = 0;

        function initAudioGesture() {
            audioAllowed = true;
            document.getElementById('gesture-overlay').classList.add('d-none');
            // Test audio pancingan agar browser membuka restriction policy 
            const audio = document.getElementById('audio-tingtong');
            audio.play().then(() => audio.pause());
        }

        // Live Clock Jam Digital
        setInterval(() => {
            const now = new Date();
            document.getElementById('live-clock').innerText = now.toLocaleString('id-ID', { dateStyle: 'full', timeStyle: 'medium' });
        }, 1000);

        // Koneksikan EventSource SSE Client
        const source = new EventSource("{{ route('antrian.stream') }}");

        source.addEventListener('queue-update', function(event) {
            const data = JSON.parse(event.data);

            if (data.antrian_sekarang) {
                const current = data.antrian_sekarang;
                
                document.getElementById('papan-nomor').innerText = String(current.nomor).padStart(3, '0');
                document.getElementById('papan-nama').innerText = current.nama;

                // Cek jika state status bernilai calling dan timestamp baru (bukan loop data lama)
                if (current.status === 'calling' && current.timestamp > lastTimestamp) {
                    lastTimestamp = current.timestamp;
                    if (audioAllowed) {
                        triggerSuaraPanggilan(current.nomor, current.nama);
                    }
                }
            } else {
                document.getElementById('papan-nomor').innerText = "000";
                document.getElementById('papan-nama').innerText = "Belum Ada Panggilan";
            }
        });

        // Fungsi Suara Panggilan Pintar (Kombinasi Sound Bell + Web Speech API)
        function triggerSuaraPanggilan(nomor, nama) {
            if (!('speechSynthesis' in window)) return;

            window.speechSynthesis.cancel();

            const bell = document.getElementById('audio-tingtong');
            bell.currentTime = 0;
            bell.play();

            // Trigger text-to-speech bahasa indonesia sesaat setelah bel selesai diputar
            bell.onended = function() {
                const kalimat = `Nomor antrian, ${nomor}. ${nama}. Silakan masuk ke ruang pemeriksaan.`;
                const utterance = new SpeechSynthesisUtterance(kalimat);
                
                utterance.lang = 'id-ID';
                utterance.rate = 0.85;
                utterance.pitch = 1.0;
                
                window.speechSynthesis.speak(utterance);
            };
        }
    </script>
</body>
</html>