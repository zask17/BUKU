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
    <div class="container mt-4">
        <h2 class="mb-4">Kunjungan Toko</h2>

        <div class="row">
            <div class="col-md-5">
                <div class="card shadow-sm border-info mb-4">
                    <div class="card-header bg-info text-white">
                        <i class="mdi mdi-map-marker"></i> Lokasi Saya Saat Ini
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 mb-2">
                                <label class="small font-weight-bold">Latitude</label>
                                <input type="text" id="my_lat" class="form-control form-control-sm" readonly
                                    placeholder="0.000">
                            </div>
                            <div class="col-6 mb-2">
                                <label class="small font-weight-bold">Longitude</label>
                                <input type="text" id="my_long" class="form-control form-control-sm" readonly
                                    placeholder="0.000">
                            </div>
                            <div class="col-12">
                                <label class="small font-weight-bold">Akurasi (Meter)</label>
                                <input type="text" id="my_acc" class="form-control form-control-sm" readonly
                                    placeholder="0">
                            </div>
                        </div>
                        <button type="button" class="btn btn-info btn-block mt-3" onclick="getMyCurrentLocation()">
                            <i class="mdi mdi-crosshairs-gps"></i> Ambil Titik GPS Saya
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card shadow-sm border-primary mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="mdi mdi-barcode-scan"></i> Scan Barcode Toko
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <div class="input-group">
                                <select id="camera-selector" class="form-control form-control-sm"></select>
                                <button type="button" class="btn btn-primary btn-sm" id="start-scanner-btn"
                                    style="display:none;">Buka Kamera</button>
                                <button type="button" class="btn btn-danger btn-sm" id="stop-scanner-btn"
                                    style="display:none;">Tutup</button>
                            </div>
                        </div>

                        <div id="reader" class="border rounded bg-dark d-none mb-3"></div>

                        <form id="form-kunjungan">
                            <div class="form-group text-left">
                                <div>
                                    <label class="font-weight-bold">ID Toko</label>
                                    <input type="text" id="barcode_input" class="form-control"
                                        placeholder="Hasil scan barcode">
                                </div>
                                <div>
                                    <label class="font-weight-bold"> Nama Toko</label>
                                    <input type="text" id="nama_toko_display" class="form-control"
                                        placeholder="Nama toko akan muncul di sini..." readonly>
                                </div>
                            </div>

                            <input type="hidden" id="final_lat">
                            <input type="hidden" id="final_long">
                            <input type="hidden" id="final_acc">

                            <button type="button" class="btn btn-success btn-lg w-100" onclick="startProcess()">
                                <i class="mdi mdi-check-circle"></i> KONFIRMASI KUNJUNGAN
                            </button>
                        </form>
                    </div>
                </div>
                <div id="status_area" class="alert alert-warning" style="display:none;"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">Daftar Koordinat Toko</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Toko</th>
                                        <th>ID Toko</th>
                                        <th>Latitude</th>
                                        <th>Longitude</th>
                                        <th>Akurasi Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($listToko as $t)
                                        <tr>
                                            <td>{{ $t->nama_toko }}</td>
                                            <td><code>{{ $t->idtoko }}</code></td>
                                            <td>{{ $t->latitude }}</td>
                                            <td>{{ $t->longtitude }}</td>
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

        function getMyCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(pos => {
                    document.getElementById('my_lat').value = pos.coords.latitude;
                    document.getElementById('my_long').value = pos.coords.longitude;
                    document.getElementById('my_acc').value = Math.round(pos.coords.accuracy);

                    document.getElementById('final_lat').value = pos.coords.latitude;
                    document.getElementById('final_long').value = pos.coords.longitude;
                    document.getElementById('final_acc').value = Math.round(pos.coords.accuracy);
                }, err => {
                    alert("Izin GPS ditolak atau gagal: " + err.message);
                }, { enableHighAccuracy: true });
            }
        }

        async function loadCameras() {
            try {
                const devices = await Html5Qrcode.getCameras();
                const select = document.getElementById('camera-selector');
                const startBtn = document.getElementById('start-scanner-btn');
                if (devices.length > 0) {
                    select.innerHTML = '';
                    devices.forEach((device, i) => {
                        select.add(new Option(device.label || `Kamera ${i + 1}`, device.id));
                    });
                    selectedCameraId = devices[0].id;
                    startBtn.style.display = 'block';
                }
            } catch (err) { console.error(err); }
        }

        document.getElementById('start-scanner-btn').addEventListener('click', () => {
            document.getElementById('reader').classList.remove('d-none');
            document.getElementById('start-scanner-btn').style.display = 'none';
            document.getElementById('stop-scanner-btn').style.display = 'block';
            html5QrCode = new Html5Qrcode("reader");
            html5QrCode.start(selectedCameraId, { fps: 10, qrbox: 250 }, (decodedText) => {
                beep.play();
                document.getElementById('barcode_input').value = decodedText;
                stopScanning();
                startProcess();
            });
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

        async function getAccuratePosition(targetAccuracy = 50, maxWait = 15000) {
            return new Promise((resolve, reject) => {
                let bestResult = null;
                const startTime = Date.now();
                const watchId = navigator.geolocation.watchPosition((position) => {
                    const acc = position.coords.accuracy;
                    if (!bestResult || acc < bestResult.coords.accuracy) {
                        bestResult = position;
                        document.getElementById('my_lat').value = position.coords.latitude;
                        document.getElementById('my_long').value = position.coords.longitude;
                        document.getElementById('my_acc').value = Math.round(acc);
                    }
                    if (acc <= targetAccuracy) {
                        navigator.geolocation.clearWatch(watchId);
                        resolve(bestResult);
                    }
                    if (Date.now() - startTime >= maxWait) {
                        navigator.geolocation.clearWatch(watchId);
                        resolve(bestResult);
                    }
                }, (error) => reject(error), { enableHighAccuracy: true, maximumAge: 0 });
            });
        }

        async function startProcess() {
            const barcode = document.getElementById('barcode_input').value;
            if (!barcode) return alert('Silakan scan barcode toko dulu!');

            const statusArea = document.getElementById('status_area');
            statusArea.style.display = 'block';
            statusArea.className = "alert alert-warning";
            statusArea.innerText = "📍 Sedang mengunci koordinat terbaik Anda...";

            try {
                const pos = await getAccuratePosition(50);

                statusArea.innerText = "⏳ Mengirim data ke server...";

                const response = await fetch("{{ route('sales.storeVisit') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
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
                statusArea.className = result.status === 'success' ? "alert alert-success" : "alert alert-danger";
                statusArea.innerText = (result.status === 'success' ? "✅ " : "❌ ") + result.message;

                if (result.status === 'success') setTimeout(() => location.reload(), 3000);

            } catch (error) {
                statusArea.className = "alert alert-danger";
                statusArea.innerText = "⚠️ Error GPS: " + error.message;
            }
        }

        document.addEventListener('DOMContentLoaded', loadCameras);
    </script>
@endsection