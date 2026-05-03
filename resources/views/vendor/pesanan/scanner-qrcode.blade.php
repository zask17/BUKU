@extends('layouts.vendor.main')

@section('title-page', 'Scanner Validasi Pesanan')

@section('style-page')
    <style>
        #reader {
            width: 100% !important; max-width: 550px; height: 400px; margin: 15px auto;
            border: 4px solid #9a55ff; border-radius: 15px; background: #000;
            overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .result-card { animation: fadeIn 0.4s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .vendor-info-box { background: #f0f0ff; border-left: 5px solid #6f42c1; padding: 15px; border-radius: 5px; }
    </style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <h5 class="mb-0"><i class="mdi mdi-qrcode-scan"></i> Scanner Pesanan Multi-Vendor</h5>
        </div>
        <div class="card-body">
            <!-- Pilihan Kamera -->
            <div class="row mb-4">
                <div class="col-md-6 offset-md-3">
                    <label class="font-weight-bold">Pilih Kamera:</label>
                    <div class="input-group">
                        <select id="camera-selector" class="form-control"></select>
                        <div class="input-group-append">
                            <button id="btn-start" class="btn btn-primary">Mulai</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="status-msg" class="alert alert-secondary d-none"></div>
            <div id="reader"></div>

            <!-- Box Hasil Validasi -->
            <div id="result-box" class="result-card d-none mt-4">
                <div class="alert alert-success border-0 shadow-sm">
                    <h4 class="text-center font-weight-bold mb-4">PESANAN TERVERIFIKASI</h4>
                    <div id="order-content" class="bg-white p-3 rounded border"></div>
                    <button class="btn btn-warning btn-block mt-4 font-weight-bold" onclick="resetScanner()">SCAN BERIKUTNYA</button>
                </div>
            </div>

            <!-- Box Error -->
            <div id="error-box" class="result-card d-none mt-4">
                <div class="alert alert-danger border-0 text-center shadow-sm">
                    <h5 class="font-weight-bold">Akses Ditolak</h5>
                    <p id="err-msg"></p>
                    <button class="btn btn-dark" onclick="resetScanner()">Coba Lagi</button>
                </div>
            </div>
        </div>
    </div>
</div>

<audio id="beep"><source src="{{ asset('audio/beep.mp3') }}" type="audio/mpeg"></audio>

<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    let qrScanner = null;
    const sound = document.getElementById('beep');
    let camId = null;

    // Load kamera saat halaman siap
    Html5Qrcode.getCameras().then(devices => {
        if (devices.length > 0) {
            const select = document.getElementById('camera-selector');
            devices.forEach((d, i) => select.add(new Option(d.label || `Cam ${i+1}`, d.id)));
            camId = devices[0].id;
        }
    });

    document.getElementById('btn-start').addEventListener('click', () => {
        qrScanner = new Html5Qrcode("reader");
        qrScanner.start(camId, { fps: 10, qrbox: 250 }, onScan);
        document.getElementById('btn-start').disabled = true;
    });

    function onScan(text) {
        sound.play().catch(() => {});
        qrScanner.pause();
        
        // Panggilan API via Axios
        axios.get(`/kantin/order-details/${text.trim()}`)
            .then(res => {
                if(res.data.success) renderOrder(res.data.data);
            })
            .catch(err => {
                document.getElementById('err-msg').innerText = err.response?.data?.message || 'Error Jaringan';
                document.getElementById('error-box').classList.remove('d-none');
            });
    }

    function renderOrder(d) {
        let items = '<ul class="list-group list-group-flush mt-2">';
        d.items.forEach(i => {
            items += `<li class="list-group-item d-flex justify-content-between small">
                <span><strong>${i.jumlah}x</strong> ${i.nama_menu}</span>
                <span class="text-primary font-weight-bold">Rp ${i.subtotal}</span>
            </li>`;
        });
        items += '</ul>';

        document.getElementById('order-content').innerHTML = `
            <div class="vendor-info-box mb-3">
                <h6 class="mb-1 font-weight-bold text-dark">Vendor: ${d.nama_vendor}</h6>
                <div class="d-flex justify-content-between">
                    <span>Subtotal Anda:</span><span class="h5 mb-0 text-primary font-weight-bold">Rp ${d.subtotal_vendor}</span>
                </div>
            </div>
            <table class="table table-sm table-borderless mb-0 small">
                <tr><td width="40%">ID Pesanan</td><td>: #${d.idpesanan}</td></tr>
                <tr><td>Pelanggan</td><td>: ${d.nama_customer}</td></tr>
                <tr><td>Total Transaksi</td><td>: Rp ${d.total_transaksi}</td></tr>
            </table>
            ${items}`;
        
        document.getElementById('result-box').classList.remove('d-none');
    }

    function resetScanner() {
        document.getElementById('result-box').classList.add('d-none');
        document.getElementById('error-box').classList.add('d-none');
        if(qrScanner) qrScanner.resume();
    }
</script>
@endsection