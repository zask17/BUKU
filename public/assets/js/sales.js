// =====================
// Config & State
// =====================
let html5QrcodeScanner = null;
let lastScannedCode = null;
let scannerObserver = null;
let salesPosition = null;
let tokoData = null;

const THRESHOLD = 300; // Jarak maksimum standar dalam meter
const barcodeConfig = window.barcodeScannerConfig || {};
const salesConfig = window.salesConfig || {};
const beepSound = document.getElementById('beepAudio') || (barcodeConfig.beepUrl ? new Audio(barcodeConfig.beepUrl) : null);

function getEl(id) {
    return document.getElementById(id);
}

// =====================
// Scanner UI Fixes
// =====================
function applyScannerUIFixes() {
    scannerObserver = new MutationObserver(() => {
        const btnStart     = getEl('html5-qrcode-button-camera-start');
        const btnStop      = getEl('html5-qrcode-button-camera-stop');
        const selectCamera = getEl('html5-qrcode-select-camera');

        if (btnStart && !btnStart.classList.contains('btn')) {
            btnStart.className = 'btn btn-gradient-primary mt-2 mx-2';
            btnStart.style.cssText = '';
            btnStart.style.display = 'inline-block';
        }
        if (btnStop && !btnStop.classList.contains('btn')) {
            btnStop.className = 'btn btn-danger mt-2';
            btnStop.style.cssText = '';
            btnStop.style.display = 'inline-block';
        }
        if (selectCamera && !selectCamera.classList.contains('form-select')) {
            selectCamera.className = 'form-select form-select-sm mt-2 mb-2 d-inline-block';
            selectCamera.style.cssText = 'width: 90%; color: #333;';
        }
    });

    const reader = getEl('reader');
    if (reader) {
        scannerObserver.observe(reader, { childList: true, subtree: true });
    }
}

function startScanner() {
    const reader = getEl('reader');
    if (!reader || html5QrcodeScanner) return;

    html5QrcodeScanner = new Html5QrcodeScanner('reader', {
        fps: 10,
        qrbox: { width: 300, height: 150 },
        rememberLastUsedCamera: true,
        supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
    }, false);

    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    applyScannerUIFixes();
}

// =====================
// Geolocation & Math
// =====================
function getAccuratePosition(targetAccuracy = 50, maxWait = 20000) {
    return new Promise((resolve, reject) => {
        let bestResult = null;
        const startTime = Date.now();
        const watchId = navigator.geolocation.watchPosition(
            (position) => {
                const acc = position.coords.accuracy;
                if (!bestResult || acc < bestResult.coords.accuracy) bestResult = position;
                if (acc <= targetAccuracy) {
                    navigator.geolocation.clearWatch(watchId);
                    resolve(bestResult);
                }
                if (Date.now() - startTime >= maxWait) {
                    navigator.geolocation.clearWatch(watchId);
                    if (bestResult) resolve(bestResult);
                    else reject(new Error('Timeout, tidak dapat posisi akurat'));
                }
            },
            (error) => reject(error),
            { enableHighAccuracy: true, maximumAge: 0, timeout: maxWait }
        );
    });
}

function haversine(lat1, lng1, lat2, lng2) {
    const R = 6371000;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLng = (lng2 - lng1) * Math.PI / 180;
    const a = Math.sin(dLat / 2) ** 2 +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
        Math.sin(dLng / 2) ** 2;
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

// =====================
// UI Helpers
// =====================
function setLoading(show, text = 'Memproses...') {
    const loading     = getEl('scan-loading');
    const loadingText = getEl('scan-loading-text');
    const content     = getEl('scan-result-content');
    if (loadingText) loadingText.textContent = text;
    if (loading) loading.classList.toggle('d-none', !show);
    if (content) content.classList.toggle('d-none', show);
}

// =====================
// Scan Core Logic
// =====================
async function onScanSuccess(decodedText) {
    decodedText = decodedText?.toString().trim();
    if (!decodedText || decodedText === lastScannedCode) return;
    lastScannedCode = decodedText;
    beepSound?.play?.().catch(() => {});

    setLoading(true, '🔍 Mencari data toko...');

    const lookupUrl = `${barcodeConfig.lookupUrl}/${encodeURIComponent(decodedText)}`;

    try {
        const response = await fetch(lookupUrl);
        const data = await response.json();

        if (!data.success) {
            setLoading(false);
            alert(data.message || 'Toko tidak ditemukan');
            lastScannedCode = null;
            return;
        }

        tokoData = data.data;
        const content = getEl('scan-result-content');
        
        // Render Hasil UI
        content.innerHTML = `
            <div class="row text-start">
                <div class="col-md-6">
                    <div class="alert alert-secondary mb-2">
                        <strong>Nama Toko:</strong> ${tokoData.nama_toko}<br>
                        <strong>Koordinat:</strong> ${tokoData.latitude}, ${tokoData.longtitude}<br>
                        <strong>Akurasi Toko:</strong> ${tokoData.accuracy} meter
                    </div>
                </div>
                <div class="col-md-6">
                    <div id="validasi-result" class="d-none">
                        <div id="validasi-alert" class="alert mb-0"></div>
                    </div>
                </div>
            </div>
            <div id="btn-kirim-wrapper" class="mt-2 d-none">
                <button type="button" id="btn-kirim-kunjungan" class="btn btn-gradient-primary btn-fw">
                    <i class="mdi mdi-check-circle"></i> Kirim Kunjungan
                </button>
            </div>
        `;

        getEl('btn-kirim-kunjungan').addEventListener('click', kirimKunjungan);

        // Ambil Lokasi Sales
        setLoading(true, '📡 Mengambil lokasi GPS akurat...');
        salesPosition = await getAccuratePosition(50, 15000);

        // Hitung Jarak
        const jarak = haversine(
            salesPosition.coords.latitude, salesPosition.coords.longitude,
            parseFloat(tokoData.latitude), parseFloat(tokoData.longtitude)
        );

        // Threshold Efektif = Jarak Maks + Akurasi Toko + Akurasi Sales
        const thresholdEfektif = THRESHOLD + parseFloat(tokoData.accuracy) + salesPosition.coords.accuracy;
        const diterima = jarak <= thresholdEfektif;
        const status   = diterima ? 'diterima' : 'ditolak';

        setLoading(false);

        // Update UI Validasi
        const validasiAlert = getEl('validasi-alert');
        validasiAlert.className = `alert ${diterima ? 'alert-success' : 'alert-danger'} mb-0`;
        validasiAlert.innerHTML = `
            <strong>${diterima ? '✅ LOKASI VALID' : '❌ LOKASI DITOLAK'}</strong><br>
            Jarak Aktual: <strong>${Math.round(jarak)} m</strong><br>
            Threshold: ${Math.round(thresholdEfektif)} m<br>
            Akurasi Anda: ${salesPosition.coords.accuracy.toFixed(1)} m
        `;
        
        getEl('validasi-result').classList.remove('d-none');
        getEl('btn-kirim-wrapper').classList.remove('d-none');

        // Persiapkan data untuk dikirim
        tokoData._kunjungan = {
            idtoko    : tokoData.idtoko,
            latitude  : salesPosition.coords.latitude,
            longitude : salesPosition.coords.longitude,
            accuracy  : salesPosition.coords.accuracy,
            jarak     : Math.round(jarak),
            status    : status
        };

    } catch (err) {
        setLoading(false);
        console.error(err);
        alert('Terjadi kesalahan sistem');
        lastScannedCode = null;
    }
}

function onScanFailure() {}

// =====================
// POST Data
// =====================
function kirimKunjungan() {
    if (!tokoData || !tokoData._kunjungan) return;

    const btn = getEl('btn-kirim-kunjungan');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengirim...';

    fetch(salesConfig.storeSalesUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': salesConfig.csrfToken
        },
        body: JSON.stringify(tokoData._kunjungan)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Reset UI
            getEl('scan-result-content').innerHTML = '<p class="text-muted">Kunjungan berhasil dicatat. Silakan scan toko berikutnya.</p>';
            getEl('btn-kirim-wrapper').classList.add('d-none');
            lastScannedCode = null;

            // Tampilkan Notif
            const notif = getEl('notification-container');
            if (notif) {
                notif.innerHTML = `<div class="alert alert-success alert-dismissible fade show">
                    Kunjungan Toko <strong>${tokoData.nama_toko}</strong> Berhasil!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;
                setTimeout(() => notif.innerHTML = '', 5000);
            }

            // Update Tabel Riwayat secara realtime
            tambahRiwayatManual(
                tokoData.nama_toko,
                tokoData._kunjungan.jarak,
                tokoData._kunjungan.accuracy,
                tokoData._kunjungan.status
            );
        } else {
            alert(data.message || 'Gagal menyimpan');
            btn.disabled = false;
            btn.innerHTML = '<i class="mdi mdi-check-circle"></i> Kirim Kunjungan';
        }
    })
    .catch(err => {
        console.error(err);
        alert('Kesalahan koneksi');
        btn.disabled = false;
    });
}

function tambahRiwayatManual(namaToko, jarak, akurasi, status) {
    const tbody = getEl('riwayat-tbody');
    const empty = getEl('riwayat-empty');
    if (empty) empty.remove();

    const count = tbody.querySelectorAll('tr').length + 1;
    const isDiterima = status === 'diterima';
    const row = document.createElement('tr');
    
    row.innerHTML = `
        <td>${String(count).padStart(2, '0')}</td>
        <td>${namaToko}</td>
        <td>${jarak} meter</td>
        <td><span class="badge ${isDiterima ? 'bg-success' : 'bg-danger'}">${isDiterima ? '✅ Diterima' : '❌ Ditolak'}</span></td>
        <td>${new Date().toLocaleTimeString('id-ID')}</td>
    `;
    tbody.insertBefore(row, tbody.firstChild);
}

// =====================
// Init
// =====================
window.addEventListener('load', () => {
    startScanner();
});