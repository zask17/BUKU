// sales-geolocation.js
let lastScannedCode = null;
let salesPosition = null;
let tokoData = null;
const THRESHOLD = 300;

function getEl(id) { return document.getElementById(id); }

// Haversine Formula (Lampiran 2)
function haversine(lat1, lng1, lat2, lng2) {
    const R = 6371000;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLng = (lng2 - lng1) * Math.PI / 180;
    const a = Math.sin(dLat/2)**2 +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLng/2)**2;
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

// Get Accurate Position (Lampiran 1)
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
                if (acc <= targetAccuracy || Date.now() - startTime >= maxWait) {
                    navigator.geolocation.clearWatch(watchId);
                    resolve(bestResult);
                }
            },
            (error) => {
                navigator.geolocation.clearWatch(watchId);
                reject(error);
            },
            { enableHighAccuracy: true, maximumAge: 0, timeout: maxWait }
        );
    });
}

function setLoading(show, text = 'Memproses...') {
    const loading = getEl('scan-loading');
    const content = getEl('scan-result-content');
    if (loading) loading.classList.toggle('d-none', !show);
    if (content) content.classList.toggle('d-none', show);
    if (getEl('loading-text')) getEl('loading-text').textContent = text;
}

// Update GPS Status
window.getCurrentLocation = function() {
    const statusEl = getEl('location-status');
    statusEl.innerHTML = `<i class="mdi mdi-loading mdi-spin"></i> Mendeteksi lokasi...`;
    statusEl.className = "alert alert-info";

    navigator.geolocation.getCurrentPosition(
        (pos) => {
            statusEl.innerHTML = `✅ Lat: ${pos.coords.latitude.toFixed(5)}, Lng: ${pos.coords.longitude.toFixed(5)}<br>Accuracy: ${pos.coords.accuracy.toFixed(1)}m`;
            statusEl.className = "alert alert-success";
        },
        (err) => {
            statusEl.innerHTML = `❌ ${err.message}`;
            statusEl.className = "alert alert-danger";
        },
        { enableHighAccuracy: true }
    );
};

// Main Process
window.processKunjungan = async function(barcode) {
    barcode = barcode?.toString().trim();
    if (!barcode || barcode === lastScannedCode) return;
    lastScannedCode = barcode;

    setLoading(true, 'Mencari data toko...');

    try {
        const lookupUrl = `${window.barcodeScannerConfig.lookupUrl}/${barcode}`;
        console.log('📍 Lookup URL:', lookupUrl);
        
        const res = await axios.get(lookupUrl);
        
        if (!res.data.success) throw new Error(res.data.message || 'Toko tidak ditemukan');

        tokoData = res.data.data;

        // Tampilkan info toko
        getEl('scan-result-content').innerHTML = `
            <div class="alert alert-secondary">
                <strong>Toko:</strong> ${tokoData.nama_toko}<br>
                <strong>Koordinat:</strong> ${parseFloat(tokoData.latitude).toFixed(6)}, ${parseFloat(tokoData.longtitude || tokoData.longitude).toFixed(6)}
            </div>
            <div id="validasi-box" class="d-none mt-3">
                <div id="validasi-alert" class="alert"></div>
                <button id="btn-kirim" class="btn btn-gradient-primary w-100">
                    <i class="mdi mdi-check-circle"></i> Kirim Kunjungan
                </button>
            </div>
        `;

        setLoading(true, '📍 Mengambil posisi GPS akurat...');
        salesPosition = await getAccuratePosition(50, 18000);

        // Hitung jarak
        const jarak = haversine(
            salesPosition.coords.latitude,
            salesPosition.coords.longitude,
            parseFloat(tokoData.latitude),
            parseFloat(tokoData.longtitude || tokoData.longitude)
        );

        const thresholdEfektif = THRESHOLD + parseFloat(tokoData.accuracy || 30) + salesPosition.coords.accuracy;
        const diterima = jarak <= thresholdEfektif;

        setLoading(false);

        const alertBox = getEl('validasi-alert');
        alertBox.className = `alert ${diterima ? 'alert-success' : 'alert-danger'}`;
        alertBox.innerHTML = `
            <strong>${diterima ? '✅ LOKASI VALID' : '❌ LOKASI DITOLAK'}</strong><br>
            Jarak aktual: <b>${Math.round(jarak)} meter</b><br>
            Toleransi efektif: ${Math.round(thresholdEfektif)} meter
        `;

        getEl('validasi-box').classList.remove('d-none');
        getEl('btn-kirim').onclick = () => simpanKunjungan(jarak, diterima ? 'diterima' : 'ditolak');

    } catch (err) {
        setLoading(false);
        console.error('❌ Error:', err);
        
        // Log response jika ada
        if (err.response) {
            console.error('Response Status:', err.response.status);
            console.error('Response Headers:', err.response.headers);
            console.error('Response Data:', err.response.data?.substring?.(0, 500) || err.response.data);
        }
        
        const msg = err.response?.data?.message || err.message || "Terjadi kesalahan saat memproses kunjungan.";
        alert(msg);
        lastScannedCode = null;
    }
};

async function simpanKunjungan(jarak, status) {
    const btn = getEl('btn-kirim');
    btn.disabled = true;
    btn.innerHTML = 'Menyimpan...';

    try {
        const payload = {
            idtoko: tokoData.idtoko,
            latitude: salesPosition.coords.latitude,
            longitude: salesPosition.coords.longitude,
            accuracy: salesPosition.coords.accuracy,
            jarak: Math.round(jarak),
            status: status
        };

        const res = await axios.post(window.salesConfig.storeUrl, payload, {
            headers: { 'X-CSRF-TOKEN': window.salesConfig.csrfToken }
        });

        if (res.data.success) {
            alert(`✅ Kunjungan ke ${tokoData.nama_toko} berhasil dicatat!`);
            location.reload();
        }
    } catch (e) {
        alert("Gagal menyimpan data kunjungan.");
        btn.disabled = false;
        btn.innerHTML = `<i class="mdi mdi-check-circle"></i> Kirim Kunjungan`;
    }
}