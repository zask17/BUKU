@extends($layout)

@section('title-page', 'Scanner Tag Harga')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('barang.index') }}">Daftar Barang</a></li>
    <li class="breadcrumb-item active" aria-current="page">Scan Barang</li>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <i class="mdi mdi-barcode-scan"></i> Barcode Scanner Barang
        </div>
        <div class="card-body text-center">
            <!-- Area Kamera -->
            <div id="reader" style="width: 100%; max-width: 500px; margin: auto; border-radius: 10px; overflow: hidden;">
            </div>

            <!-- Box Hasil Scan -->
            <div id="result-box" class="mt-4 p-4 border rounded bg-light shadow-sm" style="display:none;">
                <h4 class="text-primary mb-3"><i class="mdi mdi-information-outline"></i> Detail Barang</h4>
                <table class="table table-sm table-borderless text-left d-inline-block" style="width: auto;">
                    <tr>
                        <th class="pr-3">ID Barang</th>
                        <td>: <span id="res-id" class="font-weight-bold"></span></td>
                    </tr>
                    <tr>
                        <th class="pr-3">Nama Barang</th>
                        <td>: <span id="res-nama"></span></td>
                    </tr>
                    <tr>
                        <th class="pr-3">Harga</th>
                        <td>: <span class="text-success font-weight-bold">Rp <span id="res-harga"></span></span></td>
                    </tr>
                </table>
                <div class="mt-3">
                    <button class="btn btn-warning btn-sm" onclick="location.reload()">
                        <i class="mdi mdi-refresh"></i> Scan Lagi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Audio untuk Beep -->
    <audio id="beepAudio">
        <source src="{{ asset('audio/beep.mp3') }}" type="audio/mpeg">
    </audio>

    <!-- Library html5-qrcode & Axios -->
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        // Inisialisasi scanner dengan nama variabel barcode
        const html5BarcodeScanner = new Html5Qrcode("reader");
        const beep = document.getElementById("beepAudio");

        // Fungsi callback saat scan berhasil
        const barcodeSuccessCallback = (decodedText, decodedResult) => {
            // a. Keluarkan bunyi beep pendek
            beep.play().catch(e => console.log("Audio play failed:", e));

            // b. Scanner berhenti scan
            html5BarcodeScanner.stop().then(() => {
                console.log("Scanner stopped.");

                // c. Menampilkan data barang via Axios
                // Mengarah ke rute: /admin/barang/cek-scan/{id}
                axios.post(`/admin/barang/cek-scan/${decodedText}`, {
                    _token: "{{ csrf_token() }}"
                })
                    .then(res => {
                        if (res.data.success) {
                            document.getElementById('res-id').innerText = res.data.data.id;
                            document.getElementById('res-nama').innerText = res.data.data.nama;
                            document.getElementById('res-harga').innerText = res.data.data.harga;
                            document.getElementById('result-box').style.display = 'block';
                        } else {
                            alert(res.data.message);
                            location.reload();
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert("Terjadi kesalahan sistem saat mencari data barang.");
                        location.reload();
                    });
            });
        };

        // Konfigurasi area scan (qrbox untuk barcode biasanya lebih lebar/pendek)
        const config = {
            fps: 15,
            qrbox: { width: 300, height: 150 }
        };

        // Mulai kamera belakang (environment)
        html5BarcodeScanner.start(
            { facingMode: "environment" },
            config,
            barcodeSuccessCallback
        ).catch(err => {
            console.error("Gagal menjalankan kamera:", err);
            alert("Kamera tidak ditemukan atau izin ditolak.");
        });
    </script>
@endsection