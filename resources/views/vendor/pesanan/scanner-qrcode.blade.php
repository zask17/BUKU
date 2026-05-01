@extends('layouts.vendor.main')

@section('title-page', 'Scanner QR Code Pesanan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('vendor.pesanan') }}">Manajemen Pesanan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Scanner QR Code</li>
@endsection

@section('style-page')
    <style>
        #reader {
            width: 100% !important;
            max-width: 580px;
            height: 420px;
            margin: 20px auto;
            border: 5px solid #007bff;
            border-radius: 12px;
            background: #1a1a1a;
            overflow: hidden;
        }

        .scanner-container {
            max-width: 620px;
            margin: 0 auto;
        }

        .result-box {
            max-width: 620px;
            margin: 25px auto;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white py-3">
                        <h4 class="mb-0">
                            <i class="mdi mdi-qrcode-scan"></i> Scanner QR Code Pesanan
                        </h4>
                    </div>
                    
                    <div class="card-body scanner-container">
                        
                        <!-- Camera Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Pilih Kamera:</label>
                            <div class="input-group">
                                <select id="camera-selector" class="form-control">
                                    <option value="">-- Memuat daftar kamera --</option>
                                </select>
                                <button type="button" class="btn btn-primary" id="start-scanner-btn" style="display:none;">
                                    <i class="mdi mdi-play"></i> Mulai Scanning
                                </button>
                                <button type="button" class="btn btn-danger" id="stop-scanner-btn" style="display:none;">
                                    <i class="mdi mdi-stop"></i> Stop Scanning
                                </button>
                            </div>
                            <small class="text-muted">Pilih kamera lalu klik "Mulai Scanning"</small>
                        </div>

                        <!-- Status -->
                        <div id="scanner-status" class="alert alert-info d-none mb-3"></div>

                        <!-- Scanner Area -->
                        <div id="reader"></div>

                        <!-- Hasil Scan Sukses -->
                        <div id="result-box" class="result-box d-none">
                            <div class="alert alert-success">
                                <h4 class="alert-heading mb-3">
                                    <i class="mdi mdi-check-circle-outline"></i> Pesanan Ditemukan!
                                </h4>
                                <div id="order-info"></div>
                                <div class="mt-4 text-center">
                                    <button class="btn btn-warning" onclick="resumeScanning()">
                                        <i class="mdi mdi-refresh"></i> Scan Pesanan Lain
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Error -->
                        <div id="error-box" class="result-box d-none">
                            <div class="alert alert-danger">
                                <h4 class="alert-heading mb-3">
                                    <i class="mdi mdi-alert-circle-outline"></i> Pesanan Tidak Ditemukan
                                </h4>
                                <p id="error-message" class="mb-0"></p>
                                <div class="mt-3 text-center">
                                    <button class="btn btn-primary" onclick="resetScanner()">
                                        <i class="mdi mdi-refresh"></i> Coba Lagi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Beep Sound -->
    <audio id="beepAudio">
        <source src="{{ asset('sounds/beep.mp3') }}" type="audio/mpeg">
    </audio>

    <!-- Libraries -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        let scanner = null;
        const beep = document.getElementById('beepAudio');
        let isScanning = true;
        let selectedCameraId = null;

        async function loadCameras() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } });
                stream.getTracks().forEach(track => track.stop());

                const devices = await Html5Qrcode.getCameras();
                const select = document.getElementById('camera-selector');
                const startBtn = document.getElementById('start-scanner-btn');

                select.innerHTML = '<option value="">-- Pilih Kamera --</option>';

                if (devices.length > 0) {
                    devices.forEach((device, i) => {
                        const option = new Option(device.label || `Kamera ${i+1}`, device.id);
                        select.add(option);
                    });

                    selectedCameraId = devices[0].id;
                    select.value = selectedCameraId;
                    startBtn.style.display = 'inline-block';

                    showStatus('Kamera siap. Klik "Mulai Scanning"', 'info');
                } else {
                    showStatus('Tidak ada kamera yang ditemukan', 'danger');
                }
            } catch (err) {
                showStatus('Gagal mengakses kamera. Pastikan izin kamera diberikan.', 'danger');
            }
        }

        function showStatus(message, type = 'info') {
            const el = document.getElementById('scanner-status');
            el.className = `alert alert-${type} mb-3`;
            el.innerHTML = `<i class="mdi mdi-${type === 'danger' ? 'alert-circle' : 'check'}"></i> ${message}`;
            el.classList.remove('d-none');
        }

        document.getElementById('camera-selector').addEventListener('change', e => {
            selectedCameraId = e.target.value;
        });

        document.getElementById('start-scanner-btn').addEventListener('click', () => {
            if (selectedCameraId) initScanner();
            else alert('Silakan pilih kamera terlebih dahulu');
        });

        document.getElementById('stop-scanner-btn').addEventListener('click', stopScanning);

        function initScanner() {
            if (scanner) scanner.stop().catch(() => {});

            scanner = new Html5Qrcode("reader");

            scanner.start(
                { deviceId: { exact: selectedCameraId } },
                { fps: 12, qrbox: { width: 280, height: 280 }, aspectRatio: 1.0 },
                onScanSuccess,
                () => {}
            ).then(() => {
                document.getElementById('start-scanner-btn').style.display = 'none';
                document.getElementById('stop-scanner-btn').style.display = 'inline-block';
                showStatus('Arahkan QR Code ke area kamera', 'info');
            }).catch(err => {
                showStatus('Gagal memulai scanner: ' + err.message, 'danger');
            });
        }

        function onScanSuccess(decodedText) {
            if (!isScanning) return;
            isScanning = false;

            beep.play().catch(() => {});
            if (scanner) scanner.pause();

            axios.get(`/kantin/order-details/${decodedText.trim()}`)
                .then(res => {
                    if (res.data.success) {
                        showOrderDetails(res.data.data);
                    } else {
                        showError(res.data.message || 'Pesanan tidak ditemukan');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showError('Terjadi kesalahan saat mengambil data pesanan');
                });
        }

        function showOrderDetails(order) {
            let html = `
                <table class="table table-borderless table-sm mb-3">
                    <tr><th width="150">ID Pesanan</th><td><strong>${order.idpesanan}</strong></td></tr>
                    <tr><th>Nama Pelanggan</th><td>${order.nama}</td></tr>
                    <tr><th>Total</th><td><strong class="text-success">Rp ${order.total}</strong></td></tr>
                    <tr><th>Status</th><td><span class="badge bg-success">${order.status_bayar_text}</span></td></tr>
                </table>
                <hr>
                <h6>Menu yang Dipesan:</h6>
                <ul class="list-group">`;

            order.items.forEach(item => {
                html += `
                    <li class="list-group-item">
                        <strong>${item.nama_menu}</strong> × ${item.jumlah}
                        <span class="float-end fw-bold">Rp ${item.subtotal}</span>
                        ${item.catatan ? `<br><small class="text-muted">Catatan: ${item.catatan}</small>` : ''}
                    </li>`;
            });

            html += '</ul>';

            document.getElementById('order-info').innerHTML = html;
            document.getElementById('result-box').classList.remove('d-none');
        }

        function showError(message) {
            document.getElementById('error-message').innerHTML = message;
            document.getElementById('error-box').classList.remove('d-none');
            document.getElementById('result-box').classList.add('d-none');
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
            if (scanner) scanner.stop().catch(() => {});
            document.getElementById('start-scanner-btn').style.display = 'inline-block';
            document.getElementById('stop-scanner-btn').style.display = 'none';
        }

        // Inisialisasi
        document.addEventListener('DOMContentLoaded', loadCameras);

        // Cleanup
        window.addEventListener('beforeunload', () => {
            if (scanner) scanner.stop().catch(() => {});
        });
    </script>
@endsection