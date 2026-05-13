@extends('layouts.sales.main')

@section('style-page')
    <style>
        #reader {
            animation: slideInUp 0.5s ease-out;
            overflow: hidden;
            width: 100%;
            max-width: 500px;
            margin: auto;
        }
        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
@endsection

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Kunjungan Toko</h2>
            <button class="btn btn-info btn-sm" onclick="getManualLocation()">
                <i class="mdi mdi-crosshairs-gps"></i> Cek GPS Saya
            </button>
        </div>
        <hr>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4 shadow-sm border-primary">
                    <div class="card-body text-center">
                        <h5 class="card-title">Identifikasi Toko</h5>
                        
                        <div class="mb-3">
                            <div class="input-group">
                                <select id="camera-selector" class="form-control">
                                    <option value="">-- Memuat kamera... --</option>
                                </select>
                                <button type="button" class="btn btn-primary" id="start-scanner-btn" style="display:none;">
                                    <i class="mdi mdi-camera"></i> Buka Scan
                                </button>
                                <button type="button" class="btn btn-danger" id="stop-scanner-btn" style="display:none;">
                                    <i class="mdi mdi-camera-off"></i> Tutup
                                </button>
                            </div>
                        </div>

                        <div id="reader" class="border border-primary rounded bg-dark d-none mb-3"></div>

                        <form id="form-kunjungan">
                            <div class="form-group text-left">
                                <label>ID Toko (Hasil Scan)</label>
                                <input type="text" id="barcode_input" class="form-control mb-2" placeholder="Scan barcode atau isi manual">
                            </div>
                            
                            <div class="row text-left">
                                <div class="col-6">
                                    <label><small>Latitude</small></label>
                                    <input type="text" id="lat" class="form-control form-control-sm" readonly>
                                </div>
                                <div class="col-6">
                                    <label><small>Longitude</small></label>
                                    <input type="text" id="long" class="form-control form-control-sm" readonly>
                                </div>
                            </div>
                            <div class="form-group text-left mt-2">
                                <label><small>Akurasi GPS Sales (Meter)</small></label>
                                <input type="text" id="acc" class="form-control form-control-sm" readonly>
                            </div>

                            <button type="button" class="btn btn-success btn-lg w-100 mt-3" onclick="startProcess()">
                                <i class="mdi mdi-map-marker-check"></i> KONFIRMASI KUNJUNGAN
                            </button>
                        </form>
                    </div>
                </div>
                <div id="status_area" class="alert alert-warning" style="display:none;"></div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">Panduan Jarak</div>
                    <div class="card-body">
                        <p class="small text-muted">
                            Kunjungan akan <b>DITERIMA</b> jika jarak aktual Anda ke toko kurang dari 
                            <code>300m + Akurasi Toko + Akurasi Sales</code>
                        </p>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Toko</th>
                                        <th>ID</th>
                                        <th>Akurasi Toko</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($listToko as $t)
                                        <tr>
                                            <td>{{ $t->nama_toko }}</td>
                                            <td><code>{{ $t->idtoko }}</code></td>
                                            <td>{{ $t->accuracy }}m</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <audio id="beepAudio">
        <source src="https://assets.mixkit.co/active_storage/sfx/2571/2571-preview.mp3" type="audio/mpeg">
    </audio>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <script>
        let html5QrCode;
        const beep = document.getElementById('beepAudio');
        let selectedCameraId = null;

        // --- FUNGSI KAMERA & SCANNER ---
        async function loadCameras() {
            try {
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
                }
            } catch (err) { console.error("Gagal kamera: ", err); }
        }

        document.getElementById('start-scanner-btn').addEventListener('click', () => {
            document.getElementById('reader').classList.remove('d-none');
            document.getElementById('start-scanner-btn').style.display = 'none';
            document.getElementById('stop-scanner-btn').style.display = 'block';
            html5QrCode = new Html5Qrcode("reader");
            html5QrCode.start(selectedCameraId, { fps: 10, qrbox: 250 }, (decodedText) => {
                beep.play().catch(() => {});
                document.getElementById('barcode_input').value = decodedText;
                stopScanning();
                startProcess(); // Langsung proses saat scan sukses
            }).catch(err => alert("Error: " + err));
        });

        function stopScanning() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    document.getElementById('reader').classList.add('d-none');
                    document.getElementById('start-scanner-btn').style.display = 'block';
                    document.getElementById('stop-scanner-btn').style.display = 'none';
                });
            }
        }
        document.getElementById('stop-scanner-btn').addEventListener('click', stopScanning);

        // --- FUNGSI GPS (Sama seperti Admin Toko) ---
        function getManualLocation() {
            if (navigator.geolocation) {
                const statusArea = document.getElementById('status_area');
                statusArea.style.display = 'block';
                statusArea.innerText = "Mengambil koordinat...";
                
                navigator.geolocation.getCurrentPosition(pos => {
                    document.getElementById('lat').value = pos.coords.latitude;
                    document.getElementById('long').value = pos.coords.longitude;
                    document.getElementById('acc').value = Math.round(pos.coords.accuracy);
                    statusArea.className = "alert alert-info";
                    statusArea.innerText = "Koordinat berhasil diperbarui.";
                }, err => {
                    alert("Gagal ambil lokasi: " + err.message);
                }, { enableHighAccuracy: true });
            }
        }

        // --- FUNGSI VALIDASI KUNJUNGAN ---
        function getAccuratePosition(targetAccuracy = 50, maxWait = 20000) {
            return new Promise((resolve, reject) => {
                let bestResult = null;
                const startTime = Date.now();
                const watchId = navigator.geolocation.watchPosition((position) => {
                    const acc = position.coords.accuracy;
                    if (!bestResult || acc < bestResult.coords.accuracy) {
                        bestResult = position;
                        // Update display real-time
                        document.getElementById('lat').value = position.coords.latitude;
                        document.getElementById('long').value = position.coords.longitude;
                        document.getElementById('acc').value = Math.round(acc);
                    }
                    if (acc <= targetAccuracy) {
                        navigator.geolocation.clearWatch(watchId);
                        resolve(bestResult);
                    }
                    if (Date.now() - startTime >= maxWait) {
                        navigator.geolocation.clearWatch(watchId);
                        if (bestResult) resolve(bestResult);
                        else reject(new Error("Gagal mendapatkan sinyal GPS yang stabil."));
                    }
                }, (error) => reject(error), { enableHighAccuracy: true, maximumAge: 0, timeout: maxWait });
            });
        }

        async function startProcess() {
            const barcode = document.getElementById('barcode_input').value;
            if (!barcode) return alert('Pindai barcode toko terlebih dahulu!');

            const statusArea = document.getElementById('status_area');
            statusArea.style.display = 'block';
            statusArea.className = "alert alert-warning";
            statusArea.innerHTML = "📍 <b>Mengunci posisi GPS dengan akurasi terbaik...</b>";

            try {
                // Sesuai lampiran 1: Ambil akurasi terbaik
                const pos = await getAccuratePosition(50); 
                
                statusArea.innerText = "⏳ Memvalidasi jarak Anda ke lokasi toko...";

                const response = await fetch("{{ route('sales.storeVisit') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        barcode: barcode,
                        sales_lat: pos.coords.latitude,
                        sales_long: pos.coords.longitude,
                        sales_acc: pos.coords.accuracy
                    })
                });

                const result = await response.json();

                if (result.status === 'success') {
                    statusArea.className = "alert alert-success";
                    statusArea.innerText = "✅ " + result.message;
                    setTimeout(() => location.reload(), 2500);
                } else {
                    statusArea.className = "alert alert-danger";
                    statusArea.innerText = "❌ " + result.message;
                }
            } catch (error) {
                statusArea.className = "alert alert-danger";
                statusArea.innerText = "⚠️ Error: " + error.message;
            }
        }

        document.addEventListener('DOMContentLoaded', loadCameras);
    </script>
@endsection