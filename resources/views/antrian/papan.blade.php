@section('script-page')
    <script>
        let lastCalledId = null;

        if (!!window.EventSource) {
            const source = new EventSource("{{ route('admin.antrian.stream') }}");

            source.addEventListener('queue-update', function (e) {     
                const data = JSON.parse(e.data);

                // 1. Render data Panggilan Utama
                if (data.sedang_dipanggil) {
                    document.getElementById('papanNomor').innerText = data.sedang_dipanggil.nomor;
                    document.getElementById('papanNama').innerText = data.sedang_dipanggil.nama;
                    document.getElementById('papanPoli').innerText = data.sedang_dipanggil.nama_poli;

                    // Trigger Sistem Panggilan Suara (Mencegah pengulangan suara pada ID yang sama)
                    if (lastCalledId !== data.sedang_dipanggil.idantrian) {
                        lastCalledId = data.sedang_dipanggil.idantrian;
                        bunyiSuaraPanggilan(data.sedang_dipanggil.nomor, data.sedang_dipanggil.nama, data.sedang_dipanggil.nama_poli);
                    }
                } else {
                    document.getElementById('papanNomor').innerText = "-";
                    document.getElementById('papanNama').innerText = "Silakan Ambil Antrian";
                    document.getElementById('papanPoli').innerText = "-";
                    lastCalledId = null;
                }

                // 2. Render List Grid Antrian Berikutnya (Maksimal 4 baris data)
                let htmlGrid = '';
                const limitTunggu = data.daftar_tunggu.slice(0, 4);

                limitTunggu.forEach(item => {
                    htmlGrid += `
                        <div class="col-6">
                            <div class="card card-sub-poli p-3 text-center border">
                                <h3 class="text-primary fw-bold mb-1">${item.nomor}</h3>
                                <h6 class="text-dark fw-semibold text-truncate mb-0 text-capitalize">${item.nama}</h6>
                                <small class="text-muted text-xs">${item.nama_poli}</small>
                            </div>
                        </div>`;
                });

                document.getElementById('papanGridTunggu').innerHTML = htmlGrid || `
                    <div class="col-12 text-center py-5 text-muted">
                        <i class="mdi mdi-check-all-circle text-success d-block fs-1 mb-2"></i> Semua pasien hari ini telah dilayani
                    </div>`;
            });
        }

        // ===================== ROBOT SPEECH ANNOUNCEMENT ENGINE =====================
        function bunyiSuaraPanggilan(nomor, nama, poli) {
            if (!('speechSynthesis' in window)) {
                console.warn('Browser Anda tidak mendukung Web Speech API (Teks ke Suara).');
                return;
            }

            // Hentikan suara robot yang sedang berjalan sebelum memutar panggilan baru
            window.speechSynthesis.cancel();

            // Atur susunan kalimat pemanggilan teks Indonesia
            const kalimat = `Nomor antrian, ${nomor}. atas nama, ${nama}, silakan menuju ke, ${poli}.`;
            const utterance = new SpeechSynthesisUtterance(kalimat);
            
            utterance.lang = 'id-ID';  // Set lokalisasi Bahasa Indonesia
            utterance.rate = 0.85;     // Kecepatan bicara tempo sedang-nyaman
            utterance.pitch = 1.0;     // Pitch modulasi suara normal
            utterance.volume = 1.0;    // Volume maksimal suara browser

            window.speechSynthesis.speak(utterance);
        }
    </script>
@endsection