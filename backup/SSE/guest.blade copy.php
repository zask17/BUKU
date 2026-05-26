@extends('layouts.guest.main')

@section('title-page', 'Ambil Antrian Pasien')

@section('content')
<div class="row justify-content-center py-5">
    <div class="col-xl-5 col-lg-6 col-md-8">
        <div class="card border-0 rounded-4 shadow">
            <div class="card-body p-4 p-sm-5">
                
                <div id="tiketAntrian" class="d-none mb-5 text-center p-4 border border-2 border-dashed border-success rounded-4 bg-light">
                    <span class="badge bg-success text-white rounded-pill py-2 px-3 mb-2" id="poliTiket">POLIKLINIK</span>
                    <h4 class="text-success fw-bold mb-1">Registrasi Berhasil!</h4>
                    <p class="text-muted small mb-3">Simpan nomor antrian Anda di bawah ini:</p>
                    <div class="bg-white rounded-4 py-4 shadow-sm">
                        <h1 class="display-3 fw-bold text-dark mb-0" id="noTiket">--</h1>
                        <h5 class="text-secondary mt-2 mb-1" id="namaTiket">Nama Pasien</h5>
                        <p class="text-muted small mb-0" id="waktuTiket">Waktu</p>
                    </div>
                </div>

                <div class="text-center mb-4">
                    <h3 class="fw-bold text-primary">Ambil Antrian Baru</h3>
                    <p class="text-muted">Pilih unit layanan poliklinik tujuan Anda.</p>
                </div>

                <form id="formDaftar">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Nama Lengkap Pasien</label>
                        <input type="text" id="nama" class="form-control form-control-lg border-secondary" required placeholder="Masukkan nama pasien">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">Pilih Poliklinik</label>
                        <select id="idpoli" class="form-select form-control-lg border-secondary" required>
                            <option value="" disabled selected>-- Pilih Layanan Poli --</option>
                            @foreach($daftarPoli as $poli)
                                <option value="{{ $poli->idpoli }}">{{ $poli->nama_poli }} ({{ $poli->kode_poli }})</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" id="btnDaftar" class="btn btn-primary btn-lg w-100 fw-bold">Daftar Sekarang</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-page')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.getElementById('formDaftar').addEventListener('submit', function (e) {
        e.preventDefault();

        const btnSubmit = document.getElementById('btnDaftar');
        const namaInput = document.getElementById('nama').value;
        const idpoliInput = document.getElementById('idpoli').value;
        const token = "{{ csrf_token() }}";

        btnSubmit.disabled = true;
        btnSubmit.innerText = "Memproses...";

        axios.post("{{ route('antrian.guest.daftar') }}", {
            nama: namaInput,
            idpoli: idpoliInput
        }, {
            headers: { 'X-CSRF-TOKEN': token }
        })
        .then(res => {
            btnSubmit.disabled = false;
            btnSubmit.innerText = "Daftar Sekarang";

            if (res.data.success) {
                document.getElementById('noTiket').innerText = res.data.nomor;
                document.getElementById('namaTiket').innerText = res.data.nama;
                document.getElementById('poliTiket').innerText = res.data.nama_poli;
                document.getElementById('waktuTiket').innerText = 'Waktu Cetak: ' + new Date().toLocaleTimeString('id-ID') + ' WIB';

                const boxTiket = document.getElementById('tiketAntrian');
                boxTiket.classList.remove('d-none');
                boxTiket.scrollIntoView({ behavior: 'smooth' });

                document.getElementById('nama').value = '';
                document.getElementById('idpoli').value = '';
            }
        })
        .catch(err => {
            btnSubmit.disabled = false;
            btnSubmit.innerText = "Daftar Sekarang";
            alert('Gagal memproses pendaftaran. Coba lagi.');
        });
    });
</script>
@endsection