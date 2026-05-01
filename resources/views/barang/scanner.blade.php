@extends($layout)

@section('title-page', 'Scanner Tag Harga')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('barang.index') }}">Daftar Barang</a></li>
    <li class="breadcrumb-item active" aria-current="page">Scan Barang</li>
@endsection

@section('style-page')
    <style>
        #reader {
            border: 3px solid #0d6efd;
            border-radius: 10px;
            background: #000;
        }
        
        .scanner-status {
            font-size: 14px;
            font-weight: 600;
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
            display: none;
        }
        
        .scanner-status.scanning {
            display: block;
            background: #e7f5ff;
            color: #0d6efd;
            border: 1px solid #0d6efd;
        }
        
        .scanner-status.success {
            display: block;
            background: #d3f9d8;
            color: #2f9e44;
            border: 1px solid #2f9e44;
        }
        
        .scanner-status.error {
            display: block;
            background: #ffe0e0;
            color: #c92a2a;
            border: 1px solid #c92a2a;
        }

        .result-box-success {
            background: linear-gradient(135deg, #d3f9d8, #c3fae8) !important;
            border: 2px solid #2f9e44 !important;
            animation: slideInUp 0.5s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .barcode-format-info {
            background: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
            font-size: 12px;
            margin-top: 10px;
        }
    </style>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <i class="mdi mdi-barcode-scan"></i> Barcode Scanner Barang
        </div>
        <div class="card-body text-center">
            <!-- Camera Selection -->
            <div class="mb-3" style="max-width: 600px; margin: auto;">
                <div class="form-group">
                    <label class="form-label font-weight-bold">Pilih Kamera:</label>
                    <div class="input-group">
                        <select id="camera-selector" class="form-control">
                            <option value="">-- Memuat kamera yang tersedia --</option>
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
            </div>

            <!-- Status Scanner -->
            <div id="scanner-status" class="scanner-status scanning">
                <i class="mdi mdi-loading"></i> Menginisialisasi kamera... Pilih kamera terlebih dahulu
            </div>

            <!-- Area Kamera -->
            <div id="reader" style="width: 100%; max-width: 600px; margin: auto; height: 400px; background: #000; border-radius: 10px;">
            </div>

            <!-- Info Format Barcode -->
            <div class="barcode-format-info">
                <strong>Format Barcode yang Didukung:</strong><br>
                CODE128, EAN13, EAN8, UPC-A, UPC-E, CODE39, ITF
            </div>

            <!-- Box Hasil Scan -->
            <div id="result-box" class="mt-4 p-4 border rounded bg-light shadow-sm" style="display:none;">
                <div class="result-box-success">
                    <h4 class="text-success mb-3">
                        <i class="mdi mdi-check-circle-outline"></i> Barang Ditemukan!
                    </h4>
                    <table class="table table-sm table-borderless text-left d-inline-block" style="width: auto;">
                        <tr>
                            <th class="pr-3">ID Barang</th>
                            <td>: <span id="res-id" class="font-weight-bold text-primary"></span></td>
                        </tr>
                        <tr>
                            <th class="pr-3">Nama Barang</th>
                            <td>: <span id="res-nama" class="font-weight-bold"></span></td>
                        </tr>
                        <tr>
                            <th class="pr-3">Harga</th>
                            <td>: <span class="text-success font-weight-bold">Rp <span id="res-harga"></span></span></td>
                        </tr>
                        <tr>
                            <th class="pr-3">Format Barcode</th>
                            <td>: <span id="res-format" class="badge badge-info"></span></td>
                        </tr>
                    </table>
                    <div class="mt-3">
                        <button class="btn btn-warning btn-sm" onclick="resumeScanning()">
                            <i class="mdi mdi-refresh"></i> Scan Barang Lain
                        </button>
                    </div>
                </div>
            </div>

            <!-- Error Message -->
            <div id="error-box" class="mt-4 p-4 border rounded" style="display:none; border-color: #c92a2a; background-color: #ffe0e0;">
                <h5 class="text-danger mb-3">
                    <i class="mdi mdi-alert-circle-outline"></i> Barang Tidak Ditemukan
                </h5>
                <p id="error-message" class="text-dark mb-3"></p>
                <button class="btn btn-primary btn-sm" onclick="resetScanner()">
                    <i class="mdi mdi-refresh"></i> Coba Lagi
                </button>
            </div>
        </div>
    </div>

    <!-- Audio untuk Beep -->
    <audio id="beepAudio">
        <source src="{{ asset('audio/beep.mp3') }}" type="audio/mpeg">
    </audio>

    <!-- Library untuk barcode scanning -->
    <script src="https://unpkg.com/html5-qrcode@2.3.4/html5-qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        let html5BarcodeScanner;
        const beep = document.getElementById("beepAudio");
        let isScanning = true;
        let selectedCameraId = null;
        let availableCameras = [];
        let isInitializing = false;

        // Load dan populate daftar kamera
        async function loadAvailableCameras() {
            try {
                console.log("📷 Starting camera detection...");
                
                // Request permission terlebih dahulu
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: "environment" } 
                });
                console.log("✅ Camera permission granted");
                
                // Stop stream
                stream.getTracks().forEach(track => track.stop());

                // Dapatkan daftar device
                const devices = await Html5Qrcode.getCameras();
                console.log("📱 Available cameras:", devices);
                
                availableCameras = devices;

                const selector = document.getElementById('camera-selector');
                const startBtn = document.getElementById('start-scanner-btn');
                
                selector.innerHTML = '';

                if (devices && devices.length > 0) {
                    console.log(`✅ Found ${devices.length} camera(s)`);
                    
                    devices.forEach((device, index) => {
                        const option = document.createElement('option');
                        option.value = device.id;
                        option.text = device.label || `Kamera ${index + 1}`;
                        selector.appendChild(option);
                        console.log(`  - Camera ${index + 1}: ${option.text}`);
                    });

                    // Set camera pertama sebagai default
                    if (devices.length > 0) {
                        selectedCameraId = devices[0].id;
                        selector.value = selectedCameraId;
                        selector.disabled = false;
                        startBtn.style.display = 'block';
                        
                        document.getElementById('scanner-status').className = 'scanner-status scanning';
                        document.getElementById('scanner-status').innerHTML = '<i class="mdi mdi-check"></i> Kamera terdeteksi. Klik "Mulai Scanning" untuk memulai';
                    }
                } else {
                    console.error('❌ No camera found');
                    alert('Tidak ada kamera yang ditemukan di perangkat ini');
                    document.getElementById('scanner-status').className = 'scanner-status error';
                    document.getElementById('scanner-status').innerHTML = '<i class="mdi mdi-alert-circle"></i> Tidak ada kamera terdeteksi';
                }
            } catch (error) {
                console.error('❌ Error accessing camera:', error);
                document.getElementById('scanner-status').className = 'scanner-status error';
                document.getElementById('scanner-status').innerHTML = '<i class="mdi mdi-alert-circle"></i> Error: ' + error.message;
                alert('⚠️ Izinkan akses kamera untuk menggunakan scanner ini.\n\nError: ' + error.message);
            }
        }

        // Handle camera selection
        document.getElementById('camera-selector').addEventListener('change', function(e) {
            selectedCameraId = e.target.value;
            console.log("📍 Camera selected:", selectedCameraId);
        });

        // Handle start scanner button
        document.getElementById('start-scanner-btn').addEventListener('click', function() {
            if (selectedCameraId) {
                console.log("🚀 Starting scanner with camera:", selectedCameraId);
                initScanner();
            } else {
                alert('Pilih kamera terlebih dahulu');
            }
        });

        // Handle stop scanner button
        document.getElementById('stop-scanner-btn').addEventListener('click', function() {
            console.log("⏹️ Stopping scanner...");
            stopScanning();
        });

        // Fungsi untuk reset scanner
        function resetScanner() {
            console.log("🔄 Resetting scanner...");
            isScanning = true;
            document.getElementById('result-box').style.display = 'none';
            document.getElementById('error-box').style.display = 'none';
            document.getElementById('scanner-status').className = 'scanner-status scanning';
            document.getElementById('scanner-status').innerHTML = '<i class="mdi mdi-barcode-scan"></i> Siap scan... Arahkan barcode ke area scan';
            
            if (html5BarcodeScanner) {
                html5BarcodeScanner.resume().catch(err => {
                    console.error("Error resuming scanner:", err);
                });
            }
        }

        // Fungsi untuk stop scanning
        function stopScanning() {
            console.log("⏹️ Stopping scanner...");
            isScanning = false;
            document.getElementById('result-box').style.display = 'none';
            document.getElementById('error-box').style.display = 'none';
            
            if (html5BarcodeScanner) {
                html5BarcodeScanner.stop().then(() => {
                    console.log("✅ Scanner stopped");
                    html5BarcodeScanner = null;
                    isInitializing = false;
                }).catch(err => {
                    console.error("Error stopping scanner:", err);
                    html5BarcodeScanner = null;
                    isInitializing = false;
                });
            }

            // Toggle buttons visibility
            document.getElementById('start-scanner-btn').style.display = 'block';
            document.getElementById('stop-scanner-btn').style.display = 'none';
            document.getElementById('camera-selector').disabled = false;
            
            document.getElementById('scanner-status').className = 'scanner-status scanning';
            document.getElementById('scanner-status').innerHTML = '<i class="mdi mdi-stop"></i> Scanning dihentikan. Klik "Mulai Scanning" untuk memulai lagi';
        }

        // Fungsi untuk resume scanning
        function resumeScanning() {
            console.log("▶️ Resuming scanner...");
            isScanning = true;
            document.getElementById('result-box').style.display = 'none';
            document.getElementById('error-box').style.display = 'none';
            
            if (html5BarcodeScanner) {
                html5BarcodeScanner.resume().then(() => {
                    console.log("✅ Scanner resumed");
                    document.getElementById('scanner-status').className = 'scanner-status scanning';
                    document.getElementById('scanner-status').innerHTML = '<i class="mdi mdi-barcode-scan"></i> Kamera aktif... Arahkan barcode ke area scan';
                }).catch(err => {
                    console.error("Error resuming scanner:", err);
                    showError('Gagal melanjutkan scanning');
                });
            }
        }

        // Fungsi callback saat scan berhasil
        const barcodeSuccessCallback = (decodedText, decodedResult) => {
            if (!isScanning) return;
            isScanning = false;

            console.log("📊 Barcode detected:", decodedText);

            // Mainkan beep
            beep.play().catch(e => console.log("Audio play failed:", e));

            // Update status
            document.getElementById('scanner-status').className = 'scanner-status success';
            document.getElementById('scanner-status').innerHTML = '<i class="mdi mdi-check"></i> Barcode terdeteksi! Mencari data...';

            // Hentikan scanner
            if (html5BarcodeScanner) {
                html5BarcodeScanner.pause();
            }

            // Ambil format barcode
            const barcodeFormat = decodedResult?.result?.format || 'Unknown';
            const formatName = getFormatName(barcodeFormat);

            // POST ke backend
            axios.post(`/barang/cek-scan/${decodedText}`, {
                _token: "{{ csrf_token() }}"
            })
                .then(res => {
                    if (res.data.success) {
                        console.log("✅ Item found:", res.data.data);
                        document.getElementById('res-id').innerText = res.data.data.id;
                        document.getElementById('res-nama').innerText = res.data.data.nama;
                        document.getElementById('res-harga').innerText = res.data.data.harga;
                        document.getElementById('res-format').innerText = formatName;
                        document.getElementById('result-box').style.display = 'block';
                    } else {
                        console.warn("⚠️ Item not found");
                        showError(res.data.message || 'Barang tidak ditemukan');
                    }
                })
                .catch(err => {
                    console.error("❌ Error fetching item:", err);
                    showError('Terjadi kesalahan sistem saat mencari data barang');
                });
        };

        // Fungsi untuk menampilkan error
        function showError(message) {
            document.getElementById('error-message').innerText = message;
            document.getElementById('error-box').style.display = 'block';
            document.getElementById('scanner-status').className = 'scanner-status error';
            document.getElementById('scanner-status').innerHTML = '<i class="mdi mdi-alert-circle"></i> ' + message;
        }

        // Fungsi untuk mendapatkan nama format barcode
        function getFormatName(format) {
            const formats = {
                'CODE128': 'CODE 128',
                'EAN13': 'EAN 13',
                'EAN8': 'EAN 8',
                'UPC_A': 'UPC-A',
                'UPC_E': 'UPC-E',
                'CODE39': 'CODE 39',
                'ITF': 'ITF',
                'QR_CODE': 'QR Code'
            };
            return formats[format] || format;
        }

        // Inisialisasi scanner dengan kamera yang dipilih
        function initScanner() {
            if (!selectedCameraId || isInitializing) {
                console.warn("⚠️ Invalid state - selectedCameraId:", selectedCameraId, "isInitializing:", isInitializing);
                return;
            }

            isInitializing = true;

            // Destroy scanner lama jika ada
            if (html5BarcodeScanner) {
                html5BarcodeScanner.stop().then(() => {
                    console.log("✅ Old scanner stopped");
                    html5BarcodeScanner = null;
                    startNewScanner();
                }).catch(err => {
                    console.warn("⚠️ Error stopping old scanner:", err);
                    html5BarcodeScanner = null;
                    startNewScanner();
                });
            } else {
                startNewScanner();
            }

            function startNewScanner() {
                console.log("🎬 Initializing new scanner...");
                html5BarcodeScanner = new Html5Qrcode("reader");

                // Config untuk deteksi barcode optimal
                const config = {
                    fps: 20,
                    qrbox: { width: 300, height: 150 },
                    aspectRatio: 2,
                    disableFlip: false,
                    rememberLastUsedCamera: false
                };

                const qrCodeSuccessCallback = barcodeSuccessCallback;

                console.log("📍 Starting with device ID:", selectedCameraId);

                // Start kamera dengan device ID yang dipilih
                html5BarcodeScanner.start(
                    { 
                        deviceId: { exact: selectedCameraId }
                    },
                    config,
                    qrCodeSuccessCallback,
                    (error) => {
                        // Error callback untuk scan yang gagal
                        if (typeof error == 'string') {
                            console.log("Scanner error:", error);
                        }
                    }
                ).then(() => {
                    console.log("✅ Scanner started successfully");
                    document.getElementById('scanner-status').className = 'scanner-status scanning';
                    document.getElementById('scanner-status').innerHTML = '<i class="mdi mdi-barcode-scan"></i> Kamera aktif... Arahkan barcode ke area scan';
                    
                    // Toggle buttons visibility
                    document.getElementById('start-scanner-btn').style.display = 'none';
                    document.getElementById('stop-scanner-btn').style.display = 'block';
                    document.getElementById('camera-selector').disabled = true;
                    document.getElementById('start-scanner-btn').disabled = false;
                    isInitializing = false;
                }).catch(err => {
                    console.error("❌ Error initializing scanner:", err);
                    isInitializing = false;
                    showError('Gagal menjalankan kamera: ' + err.message);
                    document.getElementById('camera-selector').disabled = false;
                    document.getElementById('start-scanner-btn').disabled = false;
                    document.getElementById('start-scanner-btn').style.display = 'block';
                    document.getElementById('stop-scanner-btn').style.display = 'none';
                });
            }
        }

        // Cleanup function
        window.addEventListener('beforeunload', () => {
            if (html5BarcodeScanner) {
                html5BarcodeScanner.stop().catch(() => {});
            }
        });

        // Load cameras saat halaman selesai loading
        document.addEventListener('DOMContentLoaded', () => {
            console.log("🌐 Page loaded - Starting camera detection");
            loadAvailableCameras();
        });
    </script>
@endsection