@extends('layouts.sales.main')

@section('title-page', 'Dashboard Kunjungan')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Kunjungan Toko</li>
@endsection

@section('content')
    <div id="notification-container"></div>

    <div class="row">
        {{-- Statistik Kunjungan --}}
        <div class="col-md-4 stretch-card grid-margin">
            <div class="card bg-gradient-danger card-img-holder text-white">
                <div class="card-body">
                    <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">Kunjungan Hari Ini <i class="mdi mdi-map-marker-check mdi-24px float-right"></i></h4>
                    <h2 class="mb-5">{{ $riwayat->count() }}</h2>
                </div>
            </div>
        </div>

        {{-- Status GPS --}}
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Akurasi GPS Sales</h4>
                    <div id="location-status" class="alert alert-info">
                        <i class="mdi mdi-crosshairs-gps"></i> Menunggu GPS...
                    </div>
                    <button onclick="getLocation()" class="btn btn-gradient-primary btn-sm btn-icon-text">
                        <i class="mdi mdi-refresh btn-icon-prepend"></i> Perbarui GPS
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row align-items-stretch">
        {{-- Area Scanner (Sesuai Contoh Terbaru) --}}
        <div class="col-md-5 grid-margin d-flex">
            <div class="card w-100 shadow-sm border-primary" style="border-top: 3px solid;">
                <div class="card-body text-center">
                    <h4 class="card-title text-start"><i class="mdi mdi-barcode-scan"></i> Scanner Toko</h4>
                    
                    <div class="mb-3 text-start">
                        <label class="form-label small font-weight-bold">Pilih Kamera:</label>
                        <div class="input-group input-group-sm">
                            <select id="camera-selector" class="form-control text-dark">
                                <option value="">-- Memuat kamera... --</option>
                            </select>
                            <button type="button" class="btn btn-gradient-primary btn-xs" id="start-scanner-btn" style="display:none;">
                                <i class="mdi mdi-play"></i> Mulai
                            </button>
                            <button type="button" class="btn btn-danger btn-xs" id="stop-scanner-btn" style="display:none;">
                                <i class="mdi mdi-stop"></i> Stop
                            </button>
                        </div>
                    </div>

                    <div id="reader" class="bg-dark rounded-3 border border-secondary" style="width: 100%; min-height: 250px;"></div>
                    
                    <div id="scanner-status" class="badge badge-info mt-2 d-none"></div>
                </div>
            </div>
        </div>

        {{-- Hasil Validasi & Riwayat --}}
        <div class="col-md-7 grid-margin d-flex flex-column">
            {{-- Hasil Validasi Lokasi --}}
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <h4 class="card-title">Hasil Validasi Geolocation</h4>
                    
                    {{-- Loading State --}}
                    <div id="scan-loading" class="text-center d-none py-3">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 text-muted mb-0" id="scan-loading-text">Memproses...</p>
                    </div>

                    {{-- Konten Hasil --}}
                    <div id="scan-result-content">
                        <p class="text-muted text-center py-4">Silakan pilih kamera dan scan barcode toko.</p>
                    </div>
                </div>
            </div>

            {{-- Tabel Riwayat --}}
            <div class="card flex-grow-1 shadow-sm">
                <div class="card-body">
                    <h4 class="card-title">Riwayat Terkini</h4>
                    <div class="table-responsive">
                        <table class="table" id="tabel-riwayat">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Toko</th>
                                    <th>Jarak</th>
                                    <th>Status</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody id="riwayat-tbody">
                                @forelse ($riwayat as $index => $row)
                                    <tr>
                                        <td>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                        <td>{{ $row->toko->nama_toko ?? 'Unknown' }}</td>
                                        <td>{{ $row->jarak }}m</td>
                                        <td>
                                            @if (trim($row->status) === 'diterima')
                                                <span class="badge badge-success">✅ Diterima</span>
                                            @else
                                                <span class="badge badge-danger">❌ Ditolak</span>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($row->waktu)->format('H:i:s') }}</td>
                                    </tr>
                                @empty
                                    <tr id="riwayat-empty">
                                        <td colspan="5" class="text-center text-muted py-3">Belum ada kunjungan hari ini</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Beep Sound --}}
    <audio id="beepAudio">
        <source src="https://www.soundjay.com/buttons/beep-07.mp3" type="audio/mpeg">
    </audio>
@endsection

@section('style-page')
<style>
    #reader video { border-radius: 8px !important; width: 100% !important; object-fit: cover; }
    .badge { font-size: 11px; padding: 5px 10px; }
    .btn-xs { padding: 0.25rem 0.5rem; font-size: 0.75rem; }
</style>
@endsection

@section('js-page')
    {{-- Libraries --}}
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <script>
        // Config Global untuk sales.js
        window.barcodeScannerConfig = {
            lookupUrl: '{{ url('sales/barcode') }}',
            beepUrl: '{{ asset('assets/sounds/beep-07.mp3') }}'
        };
        
        window.salesConfig = {
            storeSalesUrl: '{{ route('sales.store') }}',
            csrfToken: '{{ csrf_token() }}'
        };

        let html5QrCode;
        let selectedCameraId;

        // 1. Load Kamera
        async function loadCameras() {
            try {
                const devices = await Html5Qrcode.getCameras();
                const select = document.getElementById('camera-selector');
                const startBtn = document.getElementById('start-scanner-btn');

                if (devices && devices.length > 0) {
                    select.innerHTML = '<option value="">-- Pilih Kamera --</option>';
                    devices.forEach((device, i) => {
                        const label = device.label || `Kamera ${i + 1}`;
                        select.add(new Option(label, device.id));
                    });
                    
                    // Default kamera belakang jika ada
                    const backCamera = devices.find(d => d.label.toLowerCase().includes('back'));
                    selectedCameraId = backCamera ? backCamera.id : devices[0].id;
                    select.value = selectedCameraId;
                    
                    startBtn.style.display = 'block';
                    document.getElementById('scanner-status').classList.remove('d-none');
                    document.getElementById('scanner-status').innerHTML = 'Siap Scanning';
                }
            } catch (err) {
                console.error("Gagal kamera:", err);
            }
        }

        // 2. Pilih Kamera
        document.getElementById('camera-selector').addEventListener('change', e => {
            selectedCameraId = e.target.value;
        });

        // 3. Start Scanner
        document.getElementById('start-scanner-btn').addEventListener('click', () => {
            if (!selectedCameraId) return alert('Pilih kamera dahulu');
            
            html5QrCode = new Html5Qrcode("reader");
            const config = { fps: 10, qrbox: { width: 250, height: 150 } };

            html5QrCode.start(
                selectedCameraId, 
                config, 
                onScanSuccess
            ).then(() => {
                document.getElementById('start-scanner-btn').style.display = 'none';
                document.getElementById('stop-scanner-btn').style.display = 'block';
                document.getElementById('scanner-status').innerHTML = 'Kamera Aktif';
            });
        });

        // 4. Stop Scanner
        document.getElementById('stop-scanner-btn').addEventListener('click', stopScanner);

        function stopScanner() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    document.getElementById('start-scanner-btn').style.display = 'block';
                    document.getElementById('stop-scanner-btn').style.display = 'none';
                    document.getElementById('scanner-status').innerHTML = 'Scanner Berhenti';
                });
            }
        }

        // 5. GPS Function
        function getLocation() {
            const status = document.getElementById('location-status');
            if (navigator.geolocation) {
                status.innerHTML = `<i class="mdi mdi-loading mdi-spin"></i> Mendeteksi lokasi...`;
                navigator.geolocation.getCurrentPosition((pos) => {
                    status.innerHTML = `Lat: ${pos.coords.latitude.toFixed(5)}, Lng: ${pos.coords.longitude.toFixed(5)} (Acc: ${pos.coords.accuracy.toFixed(1)}m)`;
                    status.className = "alert alert-success";
                    // Update global variable for sales.js if needed
                    window.currentSalesPos = pos;
                }, (err) => {
                    status.innerHTML = `Gagal: ${err.message}`;
                    status.className = "alert alert-danger";
                }, { enableHighAccuracy: true });
            }
        }

        // 6. Init
        document.addEventListener('DOMContentLoaded', () => {
            loadCameras();
            getLocation();
        });

    </script>
    
    {{-- Memanggil file sales.js --}}
    <script src="{{ asset('assets/js/sales.js') }}"></script>
@endsection