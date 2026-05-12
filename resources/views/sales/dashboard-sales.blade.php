@extends('layouts.sales.main')

@section('style-page')
    <style>
        #reader {
            animation: slideInUp 0.5s ease-out;
            overflow: hidden;
        }
        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
@endsection

@section('content')
<div class="container mt-4">
    <h2>Kunjungan Toko</h2>
    <hr>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="mdi mdi-barcode-scan"></i> Identifikasi Toko
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Pilih Kamera:</label>
                        <select id="camera-selector" class="form-control mb-2">
                            <option value="">-- Memuat kamera... --</option>
                        </select>
                        <div class="btn-group w-100">
                            <button type="button" class="btn btn-outline-primary" id="start-scanner-btn" style="display:none;">
                                <i class="mdi mdi-camera"></i> Buka Kamera
                            </button>
                            <button type="button" class="btn btn-outline-danger" id="stop-scanner-btn" style="display:none;">
                                <i class="mdi mdi-camera-off"></i> Tutup Kamera
                            </button>
                        </div>
                    </div>

                    <div id="reader" style="width: 100%; max-width: 400px; margin: auto;" 
                         class="border border-primary rounded bg-dark d-none"></div>

                    <div class="mt-3">
                        <h5>Masukkan ID Toko Manual / Hasil Scan</h5>
                        <div class="input-group mb-2">
                            <input type="text" id="barcode_input" class="form-control" placeholder="ID Toko (Contoh: 1)">
                            <button class="btn btn-success" onclick="startProcess()">
                                <i class="mdi mdi-map-marker-check"></i> Kunjungi Toko
                            </button>
                        </div>
                        <small class="text-muted">Pastikan GPS aktif untuk akurasi terbaik.</small>
                    </div>
                </div>
            </div>

            <div id="status_area" class="alert alert-warning" style="display:none;"></div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">List Toko Tersedia</div>
                <div class="table-responsive" style="max-height: 500px;">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nama Toko</th>
                                <th>Akurasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($listToko as $t)
                            <tr>
                                <td><code>{{ $t->idtoko }}</code></td>
                                <td>{{ $t->nama_toko }}</td>
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

<audio id="beepAudio">
    <source src="https://assets.mixkit.co/active_storage/sfx/2571/2571-preview.mp3" type="audio/mpeg">
</audio>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
let scanner;
const beep = document.getElementById('beepAudio');
let selectedCameraId = null;

// --- FUNGSI SCANNER BARCODE ---
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
    } catch (err) {
        console.error("Gagal akses kamera", err);
    }
}

document.getElementById('camera-selector').addEventListener('change', e => {
    selectedCameraId = e.target.value;
});

document.getElementById('start-scanner-btn').addEventListener('click', () => {
    if (selectedCameraId) {
        document.getElementById('reader').classList.remove('d-none');
        initScanner();
    }
});

document.getElementById('stop-scanner-btn').addEventListener('click', stopScanning);

function initScanner() {
    if (scanner) scanner.stop().catch(() => {});
    scanner = new Html5Qrcode("reader");

    scanner.start(
        { deviceId: { exact: selectedCameraId } },
        { fps: 10, qrbox: { width: 250, height: 250 } },
        (decodedText) => {
            beep.play().catch(() => {});
            document.getElementById('barcode_input').value = decodedText;
            stopScanning();
            // Otomatis proses setelah scan sukses
            startProcess();
        },
        () => {}
    ).then(() => {
        document.getElementById('start-scanner-btn').style.display = 'none';
        document.getElementById('stop-scanner-btn').style.display = 'block';
    });
}

function stopScanning() {
    if (scanner) {
        scanner.stop().then(() => {
            document.getElementById('reader').classList.add('d-none');
            document.getElementById('start-scanner-btn').style.display = 'block';
            document.getElementById('stop-scanner-btn').style.display = 'none';
        }).catch(() => {});
    }
}

// --- FUNGSI GEOLOCATION & VISIT --- [cite: 44]
function getAccuratePosition(targetAccuracy = 50, maxWait = 20000) {
    return new Promise((resolve, reject) => {
        let bestResult = null;
        const startTime = Date.now();
        const watchId = navigator.geolocation.watchPosition(
            (position) => {
                const acc = position.coords.accuracy;
                if (!bestResult || acc < bestResult.coords.accuracy) {
                    bestResult = position;
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
            },
            (error) => reject(error),
            { enableHighAccuracy: true, maximumAge: 0, timeout: maxWait }
        );
    });
}

async function startProcess() {
    const barcode = document.getElementById('barcode_input').value;
    if (!barcode) return alert('Masukkan atau Scan ID Toko!');

    const statusArea = document.getElementById('status_area');
    statusArea.style.display = 'block';
    statusArea.className = "alert alert-warning";
    statusArea.innerHTML = "📍 <b>Sedang mengunci posisi GPS...</b><br><small>Mohon jangan pindah tempat.</small>";

    try {
        const pos = await getAccuratePosition(50); 
        
        statusArea.innerText = "⏳ Memvalidasi jarak ke server...";

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
        
        if (result.status === 'success') {
            statusArea.className = "alert alert-success";
            statusArea.innerText = "✅ " + result.message;
            setTimeout(() => location.reload(), 2000);
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