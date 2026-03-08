@extends($layout)

@section('title-page', 'Buat Sertifikat')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('pdf.index') }}">Generate PDF</a></li>
    <li class="breadcrumb-item active" aria-current="page">Sertifikat</li>
@endsection

@section('content')
    <div class="row">
        {{-- Kolom Input Data (Kiri) --}}
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title text-primary"><i class="mdi mdi-certificate me-2"></i>Input Data Sertifikat</h4>
                    <p class="card-description"> Lengkapi detail sertifikat di bawah ini </p>

                    <form action="{{ route('pdf.sertifikat') }}" method="POST" target="previewFrame">
                        @csrf
                        <div class="form-group mb-2">
                            <label class="small fw-bold">Nomor Sertifikat</label>
                            <input type="text" name="nomor" class="form-control" placeholder="Contoh: 3353/B/UN3..."
                                required>
                        </div>
                        <div class="form-group mb-2">
                            <label class="small fw-bold">Nama Penerima</label>
                            <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap Penerima"
                                required>
                        </div>
                        <div class="form-group mb-2">
                            <label class="small fw-bold">Jabatan / Peran</label>
                            <input type="text" name="jabatan" class="form-control" placeholder="Contoh: Peserta / Pemateri"
                                required>
                        </div>
                        <div class="form-group mb-2">
                            <label class="small fw-bold">Nama Acara</label>
                            <textarea name="acara" class="form-control" rows="3"
                                placeholder="Masukkan nama agenda acara secara lengkap" required></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label class="small fw-bold">Tanggal Acara</label>
                            <input type="text" name="tanggal" class="form-control" placeholder="Contoh: 25 Februari 2026"
                                required>
                        </div>

                        <hr>
                        <p class="text-muted small mb-2">Data Penandatangan & Identitas:</p>

                        <div class="row">
                            <div class="col-6">
                                <input type="text" name="dekan" class="form-control mb-2" placeholder="Nama Dekan" required>
                            </div>
                            <div class="col-6">
                                <input type="text" name="nip_dekan" class="form-control mb-2" placeholder="NIP Dekan"
                                    required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <input type="text" name="koordinator" class="form-control mb-2" placeholder="Nama Koorprodi"
                                    required>
                            </div>
                            <div class="col-6">
                                <input type="text" name="nik_koor" class="form-control mb-2" placeholder="NIK Koorprodi"
                                    required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <input type="text" name="ketua" class="form-control mb-2" placeholder="Nama Ketua" required>
                            </div>
                            <div class="col-6">
                                <input type="text" name="nik_ketua" class="form-control mb-2" placeholder="NIK Ketua"
                                    required>
                            </div>
                        </div>

                        <div class="mt-3">
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

        {{-- Kolom Preview Kanan --}}
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title"><i class="mdi mdi-magnify me-2"></i>Tinjauan Hasil (Preview)</h4>
                    <div class="bg-light p-1 border rounded">
                        <iframe name="previewFrame" style="width:100%; height:650px; border:none;"></iframe>
                    </div>
                    <p class="text-muted small mt-2">Pastikan data sudah benar sebelum menekan tombol Download PDF.</p>
                </div>
            </div>
        </div>
    </div>
@endsection