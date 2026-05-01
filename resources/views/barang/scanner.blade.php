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
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
            <div id="scanner-status" class="alert alert-info d-none" role="alert">
                <i class="mdi mdi-loading"></i> Menginisialisasi kamera... Pilih kamera terlebih dahulu
            </div>

            <!-- Area Kamera -->
            <div id="reader" style="width: 100%; max-width: 600px; margin: auto; height: 400px;" class="border-3 border border-primary rounded-3 bg-dark">
            </div>

            <!-- Box Hasil Scan -->
            <div id="result-box" class="mt-4 d-none" style="max-width: 600px; margin: auto;">
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading mb-3">
                        <i class="mdi mdi-check-circle-outline"></i> Barang Ditemukan!
                    </h4>
                    <table class="table table-sm table-borderless text-start mb-3 d-inline-block" style="width: auto;">
                        <tr>
                            <th class="pe-3">ID Barang</th>
                            <td>: <span id="res-id" class="fw-bold text-primary"></span></td>
                        </tr>
                        <tr>
                            <th class="pe-3">Nama Barang</th>
                            <td>: <span id="res-nama" class="fw-bold"></span></td>
                        </tr>
                        <tr>
                            <th class="pe-3">Harga</th>
                            <td>: <span class="text-success fw-bold">Rp <span id="res-harga"></span></span></td>
                        </tr>
                        <tr>
                            <th class="pe-3">Format Barcode</th>
                            <td>: <span id="res-format" class="badge bg-info"></span></td>
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
            <div id="error-box" class="mt-4 d-none" style="max-width: 600px; margin: auto;">
                <div class="alert alert-danger" role="alert">
                    <h4 class="alert-heading mb-3">
                        <i class="mdi mdi-alert-circle-outline"></i> Barang Tidak Ditemukan
                    </h4>
                    <p id="error-message" class="mb-3"></p>
                    <button class="btn btn-primary btn-sm" onclick="resetScanner()">
                        <i class="mdi mdi-refresh"></i> Coba Lagi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Audio Beep -->
    <audio id="beepAudio">
        <source src="{{ asset('audio/beep.mp3') }}" type="audio/mpeg">
    </audio>

    <!-- Library barcode scanning -->
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
                
                // Request permission 
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: "environment" } 
                });
                console.log("✅ Camera permission granted");
                
                // Stop stream
                stream.getTracks().forEach(track => track.stop());

                // Dapat daftar device
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
                        
                        const statusAlert = document.getElementById('scanner-status');
                        statusAlert.classList.remove('d-none');
                        statusAlert.classList.remove('alert-danger');
                        statusAlert.classList.add('alert-info');
                        statusAlert.innerHTML = '<i class="mdi mdi-check"></i> Kamera terdeteksi. Klik "Mulai Scanning" untuk memulai';
                    }
                } else {
                    console.error('❌ No camera found');
                    alert('Tidak ada kamera yang ditemukan di perangkat ini');
                    const statusAlert = document.getElementById('scanner-status');
                    statusAlert.classList.remove('d-none');
                    statusAlert.classList.remove('alert-info');
                    statusAlert.classList.add('alert-danger');
                    statusAlert.innerHTML = '<i class="mdi mdi-alert-circle"></i> Tidak ada kamera terdeteksi';
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
            document.getElementById('result-box').classList.add('d-none');
            document.getElementById('error-box').classList.add('d-none');
            const statusAlert = document.getElementById('scanner-status');
            statusAlert.classList.remove('d-none');
            statusAlert.classList.remove('alert-danger');
            statusAlert.classList.add('alert-info');
            statusAlert.innerHTML = '<i class="mdi mdi-barcode-scan"></i> Siap scan... Arahkan barcode ke area scan';
            
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
            document.getElementById('result-box').classList.add('d-none');
            document.getElementById('error-box').classList.add('d-none');
            
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
            
            const statusAlert = document.getElementById('scanner-status');
            statusAlert.classList.remove('d-none');
            statusAlert.classList.remove('alert-danger');
            statusAlert.classList.add('alert-info');
            statusAlert.innerHTML = '<i class="mdi mdi-stop"></i> Scanning dihentikan. Klik "Mulai Scanning" untuk memulai lagi';
        }

        // Fungsi untuk resume scanning
        function resumeScanning() {
            console.log("▶️ Resuming scanner...");
            isScanning = true;
            document.getElementById('result-box').classList.add('d-none');
            document.getElementById('error-box').classList.add('d-none');
            
            if (html5BarcodeScanner) {
                html5BarcodeScanner.resume().then(() => {
                    console.log("✅ Scanner resumed");
                    const statusAlert = document.getElementById('scanner-status');
                    statusAlert.classList.remove('d-none');
                    statusAlert.classList.remove('alert-danger');
                    statusAlert.classList.add('alert-info');
                    statusAlert.innerHTML = '<i class="mdi mdi-barcode-scan"></i> Kamera aktif... Arahkan barcode ke area scan';
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
            const statusAlert = document.getElementById('scanner-status');
            statusAlert.classList.remove('d-none');
            statusAlert.classList.remove('alert-info');
            statusAlert.classList.add('alert-success');
            statusAlert.innerHTML = '<i class="mdi mdi-check"></i> Barcode terdeteksi! Mencari data...';

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
                    document.getElementById('result-box').classList.remove('d-none');
                })
                .catch(err => {
                    console.error("❌ Error fetching item:", err);
                    showError('Terjadi kesalahan sistem saat mencari data barang');
                });
        };

        // Fungsi menampilkan error
        function showError(message) {
            document.getElementById('error-message').innerText = message;
            document.getElementById('error-box').classList.remove('d-none');
            const statusAlert = document.getElementById('scanner-status');
            statusAlert.classList.remove('d-none');
            statusAlert.classList.remove('alert-info');
            statusAlert.classList.add('alert-danger');
            statusAlert.innerHTML = '<i class="mdi mdi-alert-circle"></i> ' + message;
        }

        // Fungsi mendapatkan nama format barcode
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
                    
                    // Update status alert class
                    document.getElementById('scanner-status').classList.remove('d-none');
                    document.getElementById('scanner-status').classList.remove('alert-danger');
                    document.getElementById('scanner-status').classList.add('alert-info');
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