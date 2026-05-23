@extends('layouts.guest.main')

@section('title-page', 'Ambil Antrian Pasien')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Ambil Antrian</li>
@endsection

@section('style-page')
    <style>
        .tiket-dashed-border {
            border: 2px dashed #00c689;
        }
    </style>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-5 col-lg-6 col-md-8">
            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-body p-4 p-sm-5">
                    
                    <div id="tiketAntrian" class="d-none mb-5 text-center p-4 tiket-dashed-border rounded-4 bg-white shadow-sm">
                        <div class="mb-3">
                            <span class="badge bg-info text-white rounded-pill py-2 px-3" id="poliTiket">Poli</span>
                        </div>
                        <h5 class="text-success mb-2 fw-bold">Pendaftaran Berhasil!</h5>
                        <p class="text-muted small mb-4">Silakan screenshot atau simpan nomor antrian Anda di bawah ini.</p>

                        <div class="bg-light rounded-4 py-4 px-3 shadow-sm">
                            <h1 class="display-4 fw-bold text-dark mb-1" id="noTiket">--</h1>
                            <h5 class="text-secondary mb-1" id="namaTiket">Nama Pasien</h5>
                            <p class="text-muted small mb-0" id="waktuTiket">Waktu</p>
                        </div>
                    </div>

                    <div class="text-center mb-4">
                        <h4 class="card-title text-primary mb-2 fw-bold">Ambil Nomor Antrian Baru</h4>
                        <p class="text-muted mb-0">Isi data di bawah untuk mendapatkan nomor antrian pasien.</p>
                    </div>

                    <form id="formDaftar">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Nama Lengkap Pasien</label>
                            <input type="text" id="nama" class="form-control form-control-lg border-secondary" required
                                placeholder="Masukkan nama lengkap">
                            <div class="invalid-feedback">Nama pasien wajib diisi.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Pilih Layanan Poli Tujuan</label>
                            <select id="idpoli" class="form-select form-control-lg border-secondary" required>
                                <option value="" disabled selected>-- Silakan Pilih Poli --</option>
                                @foreach($daftarPoli as $poli)
                                    <option value="{{ $poli->idpoli }}">{{ $poli->nama_poli }} ({{ $poli->kode_poli }})</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Silakan pilih poli tujuan.</div>
                        </div>

                        <button type="submit" id="btnDaftar" class="btn btn-gradient-primary btn-lg w-100 fw-bold">
                            Daftar Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js-page')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        console.log("Halaman Cetak Antrian Pasien Berhasil Dimuat.");

        document.getElementById('formDaftar').addEventListener('submit', function (e) {
            e.preventDefault();

            const btnSubmit = document.getElementById('btnDaftar');
            const namaInput = document.getElementById('nama').value;
            const idpoliInput = document.getElementById('idpoli').value;

            // Memastikan CSRF Token terbaca dengan benar dari meta tag atau helper directive blade
            let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                csrfToken = "{{ csrf_token() }}";
            }

            // Keamanan UX: Mencegah double submisssion saat request berlangsung
            btnSubmit.disabled = true;
            btnSubmit.innerText = "Memproses...";

            // Eksekusi pengiriman payload via Axios POST
            axios.post("{{ route('antrian.guest.daftar') }}", {
                nama: namaInput,
                idpoli: idpoliInput
            }, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => {
                // Mengembalikan status tombol pendaftaran
                btnSubmit.disabled = false;
                btnSubmit.innerText = "Daftar Sekarang";

                if (res.data.success) {
                    // Ekstraksi data antrian baru yang dikirim dari controller
                    const dataAntrian = res.data.data ? res.data.data : res.data;

                    // Mengisi elemen-elemen informasi tiket di view
                    document.getElementById('noTiket').innerText = dataAntrian.nomor;
                    document.getElementById('namaTiket').innerText = dataAntrian.nama;
                    document.getElementById('poliTiket').innerText = dataAntrian.nama_poli || 'Poliklinik';
                    document.getElementById('waktuTiket').innerText = 'Jam Cetak: ' + (dataAntrian.waktu || new Date().toLocaleTimeString('id-ID'));

                    // Memunculkan kotak tiket antrian dengan menghapus kelas d-none
                    const tiketEl = document.getElementById('tiketAntrian');
                    tiketEl.classList.remove('d-none');

                    // Reset field input form
                    document.getElementById('nama').value = '';
                    document.getElementById('idpoli').value = '';

                    // Efek auto scroll secara halus menuju elemen tiket hasil cetak
                    tiketEl.scrollIntoView({ behavior: 'smooth' });
                } else {
                    alert(res.data.message || 'Gagal mengambil nomor antrian.');
                }
            })
            .catch(err => {
                btnSubmit.disabled = false;
                btnSubmit.innerText = "Daftar Sekarang";
                console.error(err);
                alert(err.response?.data?.message || 'Terjadi kesalahan pada server. Pastikan input dan relasi database terhubung.');
            });
        });
    </script>
@endsection