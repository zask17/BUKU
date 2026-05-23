<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papan Antrian Publik - {{ $poliAktif->nama_poli ?? 'Multi Poli' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        .display-box { 
            background: linear-gradient(135deg, #1e3c72, #2a5298); 
            color: white; \r
            border-radius: 15px; 
        }
        .footer-marquee { 
            background-color: #f8f9fa; 
            border-top: 4px solid #1e3c72; 
        }
    </style>
</head>
<body class="bg-light">

    <div id="gesture-overlay" class="w-100 bg-warning text-dark text-center py-2 font-weight-bold" style="position: fixed; top:0; z-index:9999;">
        <button class="btn btn-sm btn-dark me-2" onclick="initAudioGesture()">
            <i class="fas fa-volume-up"></i> Aktifkan Suara Panggilan
        </button>
        <span class="small">Klik tombol ini terlebih dahulu agar sistem dapat mengeluarkan suara pengumuman otomatis.</span>
    </div>

    <div class="container-fluid bg-dark text-white py-2" style="margin-top: 40px;">
        <div class="container d-flex justify-content-between align-items-center">
            <span class="fw-bold"><i class="fas fa-tv me-2"></i> MONITOR PAPAN ANTRIAN</span>
            <form action="{{ route('admin.antrian.papan') }}" method="GET" class="d-flex align-items-center">
                <label class="me-2 text-nowrap text-white-50 small">Pilih Tampilan Poli:</label>
                <select name="idpoli" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach($polis as $p)
                        <option value="{{ $p->idpoli }}" {{ $selectedPoliId == $p->idpoli ? 'selected' : '' }}>
                            {{ $p->nama_poli }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <div class="container my-4">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-uppercase tracking-wide text-secondary">Papan Informasi Antrian</h2>
            <h1 class="display-5 fw-extrabold text-primary">{{ $poliAktif->nama_poli ?? 'Semua Poli' }}</h1>
        </div>

        <div class="row g-4 justify-content-center">
            <div class="col-lg-6">
                <div class="display-box p-5 text-center shadow-lg h-100 d-flex flex-column justify-content-center">
                    <h3 class="text-white-50 text-uppercase mb-3 fw-semibold">Nomor Antrian Sekarang</h3>
                    <h1 class="display-1 fw-bold tracking-tight my-2" id="papan-nomor" style="font-size: 8rem;">000</h1>
                    <div class="bg-white text-dark py-2 px-4 rounded-pill d-inline-block mx-auto mt-3 border shadow-sm">
                        <h4 class="mb-0 fw-bold" id="papan-nama">Belum Ada Panggilan</h4>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-success text-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-clock me-2"></i>Sisa Antrian Tunggu</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush" id="papan-sisa-list" style="max-height: 320px; overflow-y: auto;">
                            <li class="list-group-item text-center text-muted py-3">Antrian kosong</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="footer-marquee fixed-bottom py-3 shadow">
        <div class="container-fluid">
            <marquee behavior="scroll" direction="left" class="fw-bold text-dark fs-5">
                📢 Menuju Indonesia Sehat Bersama Fasilitas Layanan Kesehatan Modern — Budayakan mengantre demi kenyamanan dan ketertiban bersama di area poliklinik — Terima kasih atas kerjasama Anda.
            </marquee>
        </div>
    </div> --}}

    <audio id="audio-tingtong" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-84.wav" preload="auto"></audio>

    <script>
        let audioAllowed = false;
        let lastTimestamp = 0;
        const selectedPoliId = "{{ $selectedPoliId }}";
        const namaPoliAktif = "{{ $poliAktif->nama_poli ?? 'Poli' }}";

        function initAudioGesture() {
            audioAllowed = true;
            document.getElementById('gesture-overlay').style.display = 'none';
            const bell = document.getElementById('audio-tingtong');
            bell.play().then(() => { bell.pause(); bell.currentTime = 0; });
        }

        // Hubungkan stream monitor publik berdasarkan poli terpilih
        const source = new EventSource("{{ route('admin.antrian.stream') }}?idpoli=" + selectedPoliId);

        source.addEventListener('queue-update', function(e) {
            const data = JSON.parse(e.data);
            const current = data.antrian_sekarang;

            // Render Daftar Antrian Sisa
            const listSisa = document.getElementById('papan-sisa-list');
            listSisa.innerHTML = '';
            if(data.antrian_list.length === 0) {
                listSisa.innerHTML = '<li class="list-group-item text-center text-muted py-3">Tidak Ada Sisa Antrian</li>';
            } else {
                data.antrian_list.forEach(item => {
                    listSisa.innerHTML += `<li class="list-group-item d-flex justify-content-between align-items-center py-2 px-4">
                        <span class="badge bg-light text-primary fs-5 border">${String(item.nomor).padStart(3, '0')}</span>
                        <span class="fw-bold text-secondary">${item.nama}</span>
                    </li>`;
                });
            }

            // Validasi & Mainkan Suara Panggilan
            if (current) {
                document.getElementById('papan-nomor').innerText = String(current.nomor).padStart(3, '0');
                document.getElementById('papan-nama').innerText = current.nama;

                if (current.status === 'calling' && current.waktu_panggil) {
                    const currentTime = new Date(current.waktu_panggil).getTime();
                    if (currentTime > lastTimestamp) {
                        lastTimestamp = currentTime;
                        if (audioAllowed) triggerSuaraPanggilan(current.nomor, current.nama);
                    }
                }
            } else {
                document.getElementById('papan-nomor').innerText = "000";
                document.getElementById('papan-nama').innerText = "Belum Ada Panggilan";
            }
        });

        function triggerSuaraPanggilan(nomor, nama) {
            if (!('speechSynthesis' in window)) return;

            window.speechSynthesis.cancel();

            const bell = document.getElementById('audio-tingtong');
            bell.currentTime = 0;
            bell.play();

            bell.onended = function() {
                const utterance = new SpeechSynthesisUtterance(
                    `Nomor antrian ${nomor}. ${nama}. Silakan menuju ke ${namaPoliAktif}.`
                );
                utterance.lang = 'id-ID';
                utterance.rate = 0.85;
                window.speechSynthesis.speak(utterance);
            };
        }
    </script>
</body>
</html>