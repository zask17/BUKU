@extends($layout)
@section('content')
    <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card bg-gradient-primary card-img-holder text-white">
                <div class="card-body">
                    <h4 class="font-weight-normal mb-3">Sertifikat <i class="mdi mdi-certificate mdi-24px float-end"></i>
                    </h4>
                    <p>Buat sertifikat landscape dengan data penandatangan lengkap.</p>
                    <a href="{{ route('pdf.sertifikat.form') }}" class="btn btn-gradient-primary btn-lg btn-icon-text">
                        <i class="mdi mdi-file-document btn-icon-prepend"></i> Buat Sertifikat
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card bg-gradient-success card-img-holder text-white">
                <div class="card-body">
                    <h4 class="font-weight-normal mb-3">Undangan <i class="mdi mdi-email mdi-24px float-end"></i></h4>
                    <p>Buat undangan portrait dengan kop surat otomatis.</p>
                    <a href="{{ route('pdf.undangan.form') }}" class="btn btn-gradient-success btn-lg btn-icon-text">
                        <i class="mdi mdi-file-document-multiple btn-icon-prepend"></i> Buat Undangan
                    </a>
                    {{-- <i class="btn btn-sm btn-outline-light">Buka Form</a> --}}
                </div>
            </div>
        </div>
    </div>
@endsection