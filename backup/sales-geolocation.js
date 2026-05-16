/**
 * Mengambil posisi dengan akurasi terbaik sesuai Lampiran 1
 */
function getAccuratePosition(targetAccuracy = 50, maxWait = 20000) {
    return new Promise((resolve, reject) => {
        let bestResult = null;
        const startTime = Date.now();
        
        const watchId = navigator.geolocation.watchPosition(
            (position) => {
                const acc = position.coords.accuracy;
                // Simpan hasil terbaik
                if (!bestResult || acc < bestResult.coords.accuracy) {
                    bestResult = position;
                }
                // Jika sudah cukup akurat, berhenti
                if (acc <= targetAccuracy) {
                    navigator.geolocation.clearWatch(watchId);
                    resolve(bestResult);
                }
                // Jika timeout, gunakan hasil terbaik yang ada
                if (Date.now() - startTime >= maxWait) {
                    navigator.geolocation.clearWatch(watchId);
                    if (bestResult) resolve(bestResult);
                    else reject(new Error("Timeout, tidak dapat posisi"));
                }
            },
            (error) => reject(error),
            { enableHighAccuracy: true, maximumAge: 0, timeout: maxWait }
        );
    });
}

/**
 * Formula Haversine sesuai Lampiran 2 
 */
function haversine(lat1, lng1, lat2, lng2) {
    const R = 6371000;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLng = (lng2 - lng1) * Math.PI / 180;
    const a = Math.sin(dLat/2)**2 + 
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLng/2)**2;
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

/**
 * Fungsi Utama Proses Kunjungan
 */
async function processKunjungan(barcode) {
    const statusEl = document.getElementById('location-status');
    const resultContainer = document.getElementById('scan-result-content');
    const loading = document.getElementById('scan-loading');

    try {
        loading.classList.remove('d-none');
        resultContainer.innerHTML = '';

        // 1. Cari data toko berdasarkan barcode 
        const response = await axios.get(`${window.barcodeScannerConfig.lookupUrl}/${barcode}`);
        if (!response.data.success) throw new Error(response.data.message);
        const toko = response.data.data;

        // 2. Ambil lokasi sales saat ini (Akurasi Tinggi)
        statusEl.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Mencari sinyal GPS akurat...';
        const pos = await getAccuratePosition(50);
        
        const salesLat = pos.coords.latitude;
        const salesLng = pos.coords.longitude;
        const salesAcc = pos.coords.accuracy;

        // 3. Hitung Jarak & Validasi sesuai Lampiran 3 
        const jarakAktual = haversine(salesLat, salesLng, toko.latitude, toko.longitude);
        const threshold = 300; // Default threshold 
        const thresholdEfektif = threshold + toko.accuracy + salesAcc;
        
        const status = jarakAktual <= thresholdEfektif ? 'diterima' : 'ditolak';

        // 4. Kirim ke Server
        await axios.post(window.salesConfig.storeUrl, {
            idtoko: toko.idtoko,
            latitude: salesLat,
            longitude: salesLng,
            accuracy: salesAcc,
            jarak: Math.round(jarakAktual),
            status: status,
            _token: window.salesConfig.csrfToken
        });

        // Tampilkan Hasil
        loading.classList.add('d-none');
        resultContainer.innerHTML = `
            <div class="alert ${status === 'diterima' ? 'alert-success' : 'alert-danger'}">
                <h5>Status: ${status.toUpperCase()}</h5>
                <p>Toko: ${toko.nama_toko}<br>
                Jarak: ${Math.round(jarakAktual)}m (Max: ${Math.round(thresholdEfektif)}m)<br>
                Akurasi GPS Anda: ${Math.round(salesAcc)}m</p>
                <button onclick="location.reload()" class="btn btn-sm btn-light">Selesai</button>
            </div>`;
            
        statusEl.className = "alert alert-success";
        statusEl.innerHTML = "Lokasi berhasil dikirim!";

    } catch (err) {
        loading.classList.add('d-none');
        resultContainer.innerHTML = `<div class="alert alert-danger">${err.message}</div>`;
        statusEl.className = "alert alert-danger";
        statusEl.innerHTML = "Gagal memproses lokasi.";
    }
}

// Global function untuk tombol perbarui
window.getCurrentLocation = async () => {
    const statusEl = document.getElementById('location-status');
    statusEl.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Memperbarui GPS...';
    try {
        await getAccuratePosition(50);
        statusEl.className = "alert alert-success";
        statusEl.innerHTML = "GPS Siap & Akurat.";
    } catch (e) {
        statusEl.className = "alert alert-warning";
        statusEl.innerHTML = "GPS Lemah, pastikan di area terbuka.";
    }
};