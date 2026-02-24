@extends($layout)

@section('title-page', 'Buat Undangan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('pdf.index') }}">Generate PDF</a></li>
    <li class="breadcrumb-item active" aria-current="page">Undangan</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Input Konten Undangan</h4>

                    <form action="{{ route('pdf.undangan') }}" method="POST" target="undanganFrame">
                        @csrf
                        <input type="text" name="nomor" class="form-control mb-2" placeholder="Nomor Surat" required>
                        <input type="text" name="tanggal" class="form-control mb-2" placeholder="Tanggal Surat" required>
                        <input type="text" name="perihal" class="form-control mb-2" placeholder="Perihal" required>
                        <textarea name="isi_konten" class="form-control mb-2" rows="5"
                            placeholder="Isi Undangan..."></textarea>
                        <input type="text" name="acara_hari" class="form-control mb-2" placeholder="Hari/Tanggal Acara">
                        <input type="text" name="acara_tempat" class="form-control mb-2" placeholder="Tempat">
                                                <input type="text" name="dekan" class="form-control mb-2" placeholder="Nama Penandatangan">
                                                <input type="text" name="nip_dekan" class="form-control" placeholder="Masukkan NIP Dekan">
                        

                        <button type="submit" class="btn btn-gradient-success w-100 mb-2">Tampilkan Preview</button>
                        <button type="submit" name="download" value="1" class="btn btn-dark w-100">Download PDF</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tinjauan Hasil (Preview)</h4>
                    <iframe name="undanganFrame" style="width:100%; height:600px; border:1px solid #ddd;"></iframe>
                </div>
            </div>
        </div>
    </div>
@endsection