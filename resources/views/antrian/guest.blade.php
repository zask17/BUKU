@extends('layouts.guest.main')

@section('title-page', 'Pendaftaran Antrian')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Pendaftaran Antrian</li>
@endsection

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow border-0">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h3 class="mb-0"><i class="fas fa-user-plus me-2"></i>Ambil Nomor Antrian</h3>
                    </div>
                    <div class="card-body p-4">
                        @if(session('success_antrian'))
                            <div class="alert alert-success text-center mb-4 py-3">
                                <h5 class="alert-heading">Pendaftaran Berhasil!</h5>
                                <p class="mb-1">Selamat Datang, <strong>{{ session('success_antrian')['nama'] }}</strong></p>
                                <hr>
                                <span class="display-4 d-block font-weight-bold my-2 text-primary">
                                    {{ sprintf("%03d", session('success_antrian')['nomor']) }}
                                </span>
                                <small class="text-muted">Silakan tunggu nomor Anda dipanggil di ruang tunggu.</small>
                            </div>
                        @endif

                    <form action="{{ route('antrian.daftar') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Nama Lengkap</label>
                            <input type="text" class="form-control form-control-lg" name="nama" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label font-weight-bold">Pilih Poli</label>
                            <select class="form-select form-select-lg" name="idpoli" required>
                                <option value="">-- Pilih Poli --</option>
                                @foreach(\App\Models\Poli::whereNull('deleted_at')->get() as $p)
                                    <option value="{{ $p->idpoli }}">{{ $p->nama_poli }} ({{ $p->kode_poli }})</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3">
                            <i class="fas fa-ticket-alt me-2"></i> Ambil Nomor Antrian
                        </button>
                    </form>
                        {{-- <form action="{{ route('antrian.daftar') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="nama" class="form-label font-weight-bold">Masukkan Nama Anda</label>
                                <input type="text" class="form-control form-control-lg" id="nama" name="nama" placeholder="Contoh: Zaskia Rania" required autocomplete="off">
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100 py-2">
                                <i class="fas fa-ticket-alt me-2"></i>Daftar Antrian
                            </button>
                        </form> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
