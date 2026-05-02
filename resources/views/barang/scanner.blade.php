@extends($layout)

@section('title-page', 'Scanner Tag Harga')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('barang.index') }}">Daftar Barang</a></li>
    <li class="breadcrumb-item active" aria-current="page">Scan Barang</li>
@endsection

@section('style-page')
    <style>
        #reader {
            animation: slideInUp 0.5s ease-out;
        }
        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <i class="mdi mdi-barcode-scan"></i> Barcode Scanner Barang
        </div>
        
        <div class="card-body text-center">
            <!-- Pilih Kamera -->
            <div class="mb-4" style="max-width: 600px; margin: auto;">
                <label class="form-label font-weight-bold">Pilih Kamera:</label>
                <div class="input-group">
                    <select id="camera-selector" class="form-control">
                        <option value="">-- Memuat kamera... --</option>
                    </select>
                    <button type="button" class="btn btn-primary" id="start-scanner-btn" style="display:none;">
                        <i class="mdi mdi-play"></i> Mulai Scanning
                    </button>
                    <button type="button" class="btn btn-danger" id="stop-scanner-btn" style="display:none;">
                        <i class="mdi mdi-stop"></i> Stop Scanning
                    </button>
                </div>
                <small class="text-muted">Pilih kamera kemudian klik "Mulai Scanning"</small>
            </div>

            <!-- Status Kamera -->
            <div id="scanner-status" class="alert alert-info d-none">
                <i class="mdi mdi-loading"></i> Menginisialisasi kamera...
            </div>

            <!-- Reader Area -->
            <div id="reader" style="width: 100%; max-width: 600px; margin: auto; height: 400px;" 
                 class="border-3 border border-primary rounded-3 bg-dark"></div>

            <!-- Hasil Scan Sukses -->
            <div id="result-box" class="mt-4 d-none" style="max-width: 600px; margin: auto;">
                <div class="alert alert-success">
                    <h4 class="alert-heading mb-3">
                        <i class="mdi mdi-check-circle-outline"></i> Barang Ditemukan!
                    </h4>
                    <table class="table table-sm table-borderless text-start">
                        <tr><th width="120">ID Barang</th><td>: <span id="res-id" class="fw-bold text-primary"></span></td></tr>
                        <tr><th>Nama Barang</th><td>: <span id="res-nama" class="fw-bold"></span></td></tr>
                        <tr><th>Harga</th><td>: <span class="text-success fw-bold">Rp <span id="res-harga"></span></span></td></tr>
                        <tr><th>Format</th><td>: <span id="res-format" class="badge bg-info"></span></td></tr>
                    </table>
                    <button class="btn btn-warning btn-sm" onclick="resumeScanning()">
                        <i class="mdi mdi-refresh"></i> Scan Barang Lain
                    </button>
                </div>
            </div>

            <!-- Error -->
            <div id="error-box" class="mt-4 d-none" style="max-width: 600px; margin: auto;">
                <div class="alert alert-danger">
                    <h4 class="alert-heading mb-3">
                        <i class="mdi mdi-alert-circle-outline"></i> Barang Tidak Ditemukan
                    </h4>
                    <p id="error-message"></p>
                    <button class="btn btn-primary btn-sm" onclick="resetScanner()">
                        <i class="mdi mdi-refresh"></i> Coba Lagi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Beep Sound -->
    <audio id="beepAudio">
        <source src="{{ asset('audio/beep.mp3') }}" type="audio/mpeg">
    </audio>

    <!-- Libraries -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        let scanner;
        const beep = document.getElementById('beepAudio');
        let isScanning = true;
        let selectedCameraId = null;

        // Load Kamera
        async function loadCameras() {
            try {
                // Request permission
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                stream.getTracks().forEach(t => t.stop());

                const devices = await Html5Qrcode.getCameras();
                const select = document.getElementById('camera-selector');
                const startBtn = document.getElementById('start-scanner-btn');

                select.innerHTML = '<option value="">-- Pilih Kamera --</option>';

                if (devices.length > 0) {
                    devices.forEach((device, i) => {
                        const opt = new Option(device.label || `Kamera ${i+1}`, device.id);
                        select.add(opt);
                    });
                    selectedCameraId = devices[0].id;
                    select.value = selectedCameraId;
                    startBtn.style.display = 'block';
                    
                    document.getElementById('scanner-status').classList.remove('d-none');
                    document.getElementById('scanner-status').innerHTML = '<i class="mdi mdi-check"></i> Siap scan - Klik Mulai Scanning';
                }
            } catch (err) {
                document.getElementById('scanner-status').classList.remove('d-none');
                document.getElementById('scanner-status').classList.add('alert-danger');
                document.getElementById('scanner-status').innerHTML = 'Gagal mengakses kamera. Pastikan izin diberikan.';
            }
        }

        // Pilih kamera
        document.getElementById('camera-selector').addEventListener('change', e => {
            selectedCameraId = e.target.value;
        });

        // Mulai Scanning
        document.getElementById('start-scanner-btn').addEventListener('click', () => {
            if (selectedCameraId) initScanner();
        });

        // Stop Scanning
        document.getElementById('stop-scanner-btn').addEventListener('click', stopScanning);

        function initScanner() {
            if (scanner) scanner.stop().catch(() => {});

            scanner = new Html5Qrcode("reader");

            scanner.start(
                { deviceId: { exact: selectedCameraId } },
                { fps: 15, qrbox: { width: 300, height: 200 } },
                onScanSuccess,
                () => {} // silent error
            ).then(() => {
                document.getElementById('start-scanner-btn').style.display = 'none';
                document.getElementById('stop-scanner-btn').style.display = 'block';
                document.getElementById('scanner-status').innerHTML = '<i class="mdi mdi-barcode-scan"></i> Arahkan barcode ke kamera';
            }).catch(err => {
                alert('Gagal memulai scanner: ' + err.message);
            });
        }

        function onScanSuccess(decodedText, decodedResult) {
            if (!isScanning) return;
            isScanning = false;

            beep.play().catch(() => {});
            
            // Pause scanner
            if (scanner) scanner.pause();

            // Ambil data barang
            axios.post(`/barang/cek-scan/${decodedText}`, {
                _token: "{{ csrf_token() }}"
            })
            .then(res => {
                if (res.data.success) {
                    document.getElementById('res-id').textContent = res.data.data.id;
                    document.getElementById('res-nama').textContent = res.data.data.nama;
                    document.getElementById('res-harga').textContent = res.data.data.harga;
                    document.getElementById('res-format').textContent = decodedResult.result.format || 'QR Code';
                    document.getElementById('result-box').classList.remove('d-none');
                } else {
                    showError(res.data.message || 'Barang tidak ditemukan');
                }
            })
            .catch(() => {
                showError('Terjadi kesalahan saat mengambil data');
            });
        }

        function showError(message) {
            document.getElementById('error-message').textContent = message;
            document.getElementById('error-box').classList.remove('d-none');
        }

        function resumeScanning() {
            isScanning = true;
            document.getElementById('result-box').classList.add('d-none');
            document.getElementById('error-box').classList.add('d-none');
            if (scanner) scanner.resume();
        }

        function resetScanner() {
            resumeScanning();
        }

        function stopScanning() {
            if (scanner) {
                scanner.stop().catch(() => {});
            }
            document.getElementById('start-scanner-btn').style.display = 'block';
            document.getElementById('stop-scanner-btn').style.display = 'none';
        }

        // Inisialisasi
        document.addEventListener('DOMContentLoaded', loadCameras);

        window.addEventListener('beforeunload', () => {
            if (scanner) scanner.stop().catch(() => {});
        });
    </script>
@endsection