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
            border: 5px solid #9a55ff;
            border-radius: 12px;
            background: #1a1a1a;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .scanner-container { max-width: 620px; margin: 0 auto; }
        .result-box { max-width: 620px; margin: 25px auto; animation: fadeIn 0.5s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .vendor-total-section { background-color: #f8f9fa; border-radius: 8px; padding: 15px; border-left: 5px solid #9a55ff; }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white py-3">
                        <h4 class="mb-0"><i class="mdi mdi-qrcode-scan"></i> Scanner QR Code Pesanan</h4>
                    </div>
                    
                    <div class="card-body scanner-container">
                        <!-- Camera Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Pilih Kamera:</label>
                            <div class="input-group">
                                <select id="camera-selector" class="form-control">
                                    <option value="">-- Memuat daftar kamera --</option>
                                </select>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" id="start-scanner-btn" style="display:none;">
                                        <i class="mdi mdi-play"></i> Mulai Scanning
                                    </button>
                                    <button type="button" class="btn btn-danger" id="stop-scanner-btn" style="display:none;">
                                        <i class="mdi mdi-stop"></i> Stop Scanning
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="scanner-status" class="alert alert-info d-none mb-3"></div>
                        <div id="reader"></div>

                        <!-- Hasil Scan Sukses -->
                        <div id="result-box" class="result-box d-none">
                            <div class="alert alert-success border-0 shadow-sm text-dark">
                                <h4 class="alert-heading mb-4 text-center text-success font-weight-bold">
                                    <i class="mdi mdi-check-circle-outline"></i> Pesanan Terverifikasi!
                                </h4>
                                <div id="order-info" class="bg-white p-3 rounded border"></div>
                                <div class="mt-4 text-center">
                                    <button class="btn btn-warning btn-lg px-5 font-weight-bold shadow" onclick="resumeScanning()">
                                        <i class="mdi mdi-refresh"></i> SCAN PESANAN LAIN
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Error Box -->
                        <div id="error-box" class="result-box d-none">
                            <div class="alert alert-danger border-0 shadow-sm text-center">
                                <h4 class="alert-heading mb-3"><i class="mdi mdi-alert-circle-outline"></i> Gagal Validasi</h4>
                                <p id="error-message" class="mb-3"></p>
                                <button class="btn btn-primary" onclick="resetScanner()"><i class="mdi mdi-refresh"></i> Coba Lagi</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <audio id="beepAudio"><source src="{{ asset('audio/beep.mp3') }}" type="audio/mpeg"></audio>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        let scanner = null;
        const beep = document.getElementById('beepAudio');
        let isScanning = true;
        let selectedCameraId = null;

        async function loadCameras() {
            try {
                const devices = await Html5Qrcode.getCameras();
                const select = document.getElementById('camera-selector');
                if (devices.length > 0) {
                    select.innerHTML = '<option value="">-- Pilih Kamera --</option>';
                    devices.forEach((device, i) => select.add(new Option(device.label || `Kamera ${i+1}`, device.id)));
                    selectedCameraId = devices[0].id;
                    select.value = selectedCameraId;
                    document.getElementById('start-scanner-btn').style.display = 'inline-block';
                }
            } catch (err) { showStatus('Gagal akses kamera. Izinkan izin kamera.', 'danger'); }
        }

        function showStatus(msg, type = 'info') {
            const el = document.getElementById('scanner-status');
            el.className = `alert alert-${type} mb-3 shadow-sm`;
            el.innerHTML = `<i class="mdi mdi-information"></i> ${msg}`;
            el.classList.remove('d-none');
        }

        document.getElementById('start-scanner-btn').addEventListener('click', () => {
            if (scanner) scanner.stop().catch(() => {});
            scanner = new Html5Qrcode("reader");
            scanner.start(selectedCameraId, { fps: 12, qrbox: 280 }, onScanSuccess)
                .then(() => {
                    document.getElementById('start-scanner-btn').style.display = 'none';
                    document.getElementById('stop-scanner-btn').style.display = 'inline-block';
                    showStatus('Scanner Aktif. Arahkan ke QR Code.', 'info');
                });
        });

        function onScanSuccess(decodedText) {
            if (!isScanning) return;
            isScanning = false;
            beep.play().catch(() => {});
            scanner.pause();

            const idPesanan = decodedText.trim(); 

            axios.get(`/kantin/order-details/${idPesanan}`)
                .then(res => {
                    if (res.data.success) showOrderDetails(res.data.data);
                    else showError(res.data.message);
                })
                .catch(err => showError(err.response?.data?.message || 'Terjadi kesalahan sistem'));
        }

        function showOrderDetails(order) {
            let itemsHtml = '<ul class="list-group list-group-flush border-top mt-3">';
            order.items.forEach(item => {
                itemsHtml += `
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <div><strong>${item.nama_menu}</strong><br><small>${item.jumlah} x Rp ${item.harga}</small></div>
                        <span class="font-weight-bold">Rp ${item.subtotal}</span>
                    </li>`;
            });
            itemsHtml += '</ul>';

            document.getElementById('order-info').innerHTML = `
                <table class="table table-borderless table-sm mb-3">
                    <tr><th>ID Pesanan</th><td>: #${order.idpesanan}</td></tr>
                    <tr><th>Customer</th><td>: ${order.nama}</td></tr>
                    <tr><th>Status</th><td>: <span class="badge badge-success">Lunas</span></td></tr>
                </table>
                <div class="vendor-total-section mb-3">
                    <div class="d-flex justify-content-between"><span>Total Pesanan:</span><span>Rp ${order.total}</span></div>
                </div>
                <h6 class="font-weight-bold text-primary mb-2">Menu untuk Vendor Anda:</h6>
                ${itemsHtml}`;
            
            document.getElementById('result-box').classList.remove('d-none');
            document.getElementById('scanner-status').classList.add('d-none');
        }

        function showError(msg) {
            document.getElementById('error-message').innerText = msg;
            document.getElementById('error-box').classList.remove('d-none');
            document.getElementById('result-box').classList.add('d-none');
        }

        function resumeScanning() {
            isScanning = true;
            document.getElementById('result-box').classList.add('d-none');
            document.getElementById('error-box').classList.add('d-none');
            if (scanner) scanner.resume();
        }

        function resetScanner() { resumeScanning(); }

        document.getElementById('stop-scanner-btn').addEventListener('click', () => {
            if (scanner) scanner.stop().then(() => location.reload());
        });

        document.addEventListener('DOMContentLoaded', loadCameras);
    </script>
@endsection