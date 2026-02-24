@extends('layouts.purple-free')

@section('title-page', 'Tinjau Dokumen PDF')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('pdf.index') }}">Generate PDF</a></li>
    <li class="breadcrumb-item active">Preview</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title text-primary">
                    <i class="mdi mdi-eye me-2"></i> Tinjauan {{ session('pdf_type') === 'sertifikat' ? 'Sertifikat' : 'Undangan' }}
                </h4>

                <div class="embed-responsive embed-responsive-4by3 mb-4" style="height: 800px;">
                    <iframe src="{{ asset(session('pdf_path')) }}" class="embed-responsive-item" style="width: 100%; height: 100%; border: none;"></iframe>
                </div>

                <div class="text-center">
                    <a href="{{ route('pdf.download') }}" class="btn btn-gradient-success btn-lg btn-icon-text">
                        <i class="mdi mdi-download btn-icon-prepend"></i> Download PDF
                    </a>
                    <a href="{{ route('pdf.index') }}" class="btn btn-gradient-secondary btn-lg btn-icon-text ms-3">
                        <i class="mdi mdi-arrow-left btn-icon-prepend"></i> Kembali ke Generate
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection