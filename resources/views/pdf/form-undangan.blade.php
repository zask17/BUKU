@extends($layout)

@section('title-page', 'Buat Undangan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('pdf.index') }}">Generate PDF</a></li>
    <li class="breadcrumb-item active" aria-current="page">Undangan</li>
@endsection

@section('content')
    <div class="row">
        {{-- Kolom Input Data (Kiri) --}}
        <div class="col-md-5 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-primary"><i class="mdi mdi-certificate me-2"></i>Input Data Undangan</h4>
                    <p class="card-description"> Lengkapi detail surat undangan resmi di bawah ini </p>

                    <form class="forms-sample" action="{{ route('pdf.undangan') }}" method="POST" target="undanganFrame">
                        @csrf
                        <div class="form-group mb-2">
                            <label class="small fw-bold" for="nomor">Nomor Surat</label>
                            <input type="text" name="nomor" class="form-control" id="nomor"
                                placeholder="Contoh: 001/UN3/2026" required>
                        </div>
                        <div class="form-group mb-2">
                            <label class="small fw-bold" for="tanggal">Tanggal Surat</label>
                            <input type="text" name="tanggal" class="form-control" id="tanggal"
                                placeholder="Surabaya, 10 Maret 2026" required>
                        </div>
                        <div class="form-group mb-2">
                            <label class="small fw-bold" for="perihal">Perihal</label>
                            <input type="text" name="perihal" class="form-control" id="perihal"
                                placeholder="Perihal Undangan" required>
                        </div>
                        <div class="form-group mb-2">
                            <label class="small fw-bold" for="isi">Isi Pesan Utama</label>
                            <textarea name="isi_konten" class="form-control" id="isi" rows="4"
                                placeholder="Masukkan isi pesan undangan..."></textarea>
                        </div>
                        <div class="form-group mb-2">
                            <label class="small fw-bold" for="hari">Hari/Tanggal Acara</label>
                            <input type="text" name="acara_hari" class="form-control" id="hari"
                                placeholder="Senin, 12 Maret 2026">
                        </div>
                        <div class="form-group mb-2">
                            <label class="small fw-bold" for="tempat">Tempat Acara</label>
                            <input type="text" name="acara_tempat" class="form-control" id="tempat"
                                placeholder="Gedung Utama Lantai 3">
                        </div>
                        <div class="form-group mb-2">
                            <label class="small fw-bold" for="dekan">Nama Penandatangan</label>
                            <input type="text" name="dekan" class="form-control" id="dekan"
                                placeholder="Nama Dekan / Pejabat Resmi">
                        </div>
                        <div class="form-group mb-2">
                            <label class="small fw-bold" for="nip">NIP Penandatangan</label>
                            <input type="text" name="nip_dekan" class="form-control" id="nip" placeholder="Masukkan NIP">
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-gradient-primary btn-icon-text w-100 mb-2">
                                <i class="mdi mdi-eye btn-icon-prepend"></i> Tampilkan Preview
                            </button>
                            <button type="submit" name="download" value="1" class="btn btn-dark btn-icon-text w-100">
                                <i class="mdi mdi-download btn-icon-prepend"></i> Download PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Kolom Preview (Kanan) --}}
        <div class="col-md-7 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tinjauan Hasil (Preview)</h4>
                    <p class="card-description"> Pastikan semua detail surat sudah sesuai sebelum dicetak </p>
                    <div class="template-demo">
                        <div class="bg-light p-1 border rounded">
                            <iframe name="undanganFrame" style="width:100%; height:750px; border:none;"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection