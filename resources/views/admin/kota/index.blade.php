@extends('layouts.admin.main')

@section('title-page', 'Manajemen Kota')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Kota</li>
@endsection

@section('content')
<div class="row">
    {{-- CARD 1: Select Biasa --}}
    <div class="col-12 col-xl-6 grid-margin stretch-card">
        <div class="card shadow-sm">
            <div class="card-header bg-gradient-primary text-white py-3">
                <h4 class="card-title mb-0 text-white">Select Biasa</h4>
            </div>
            <div class="card-body">
                <div class="form-group mb-4">
                    <label for="input_kota_1" class="font-weight-bold">Masukkan Nama Kota:</label>
                    <div class="input-group">
                        <input type="text" id="input_kota_1" class="form-control" placeholder="Contoh: Surabaya" autocomplete="off">
                        <div class="input-group-append">
                            <button class="btn btn-success px-4" type="button" id="btn_tambah_1">
                                <i class="mdi mdi-plus me-1"></i>Tambah
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label for="select_kota_1" class="font-weight-bold">Pilih Kota:</label>
                    <select id="select_kota_1" class="form-control custom-select">
                        <option value="">-- Pilih Kota --</option>
                    </select>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <p class="text-muted small mb-1 text-uppercase font-weight-bold">Kota Terpilih:</p>
                    <div class="p-2 bg-light rounded shadow-sm border-left border-primary">
                        <span id="terpilih_1" class="text-primary font-weight-bold h5 mb-0">-</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CARD 2: Select 2 --}}
    <div class="col-12 col-xl-6 grid-margin stretch-card">
        <div class="card shadow-sm">
            <div class="card-header bg-gradient-info text-white py-3">
                <h4 class="card-title mb-0 text-white">Select 2 (Searchable)</h4>
            </div>
            <div class="card-body">
                <div class="form-group mb-4">
                    <label for="input_kota_2" class="font-weight-bold">Masukkan Nama Kota:</label>
                    <div class="input-group">
                        <input type="text" id="input_kota_2" class="form-control" placeholder="Contoh: Bandung" autocomplete="off">
                        <div class="input-group-append">
                            <button class="btn btn-success px-4" type="button" id="btn_tambah_2">
                                <i class="mdi mdi-plus me-1"></i>Tambah
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label for="select_kota_2" class="font-weight-bold">Pilih Kota (Select2):</label>
                    <select id="select_kota_2" class="form-control select2-element">
                        <option value="">-- Pilih Kota --</option>
                    </select>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <p class="text-muted small mb-1 text-uppercase font-weight-bold">Kota Terpilih:</p>
                    <div class="p-2 bg-light rounded shadow-sm border-left border-info">
                        <span id="terpilih_2" class="text-info font-weight-bold h5 mb-0">-</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css-page')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: calc(2.25rem + 2px);
            padding: 0.375rem 0.75rem;
            border: 1px solid #ebedf2;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 34px;
        }
    </style>
@endsection

@section('js-page')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi Select2
            $('.select2-element').select2({
                placeholder: "-- Pilih Kota --",
                allowClear: true,
                width: '100%'
            });

            // Card 1 Logic: Select Biasa
            $('#btn_tambah_1').click(function() {
                const btn = this;
                const kota = $('#input_kota_1').val().trim();
                
                if (kota) {
                    // Aktifkan loader
                    setButtonLoading(btn, 'Menambahkan...');

                    // Simulasi delay proses agar loader terlihat
                    setTimeout(() => {
                        $('#select_kota_1').append(new Option(kota, kota));
                        $('#input_kota_1').val('').focus();
                        
                        // Kembalikan tombol ke semula
                        resetButtonLoading(btn);
                    }, 800);
                }
            });

            $('#select_kota_1').change(function() {
                $('#terpilih_1').text($(this).val() || '-');
            });

            // Card 2 Logic: Select 2
            $('#btn_tambah_2').click(function() {
                const btn = this;
                const kota = $('#input_kota_2').val().trim();
                
                if (kota) {
                    // Aktifkan loader
                    setButtonLoading(btn, 'Menambahkan...');

                    setTimeout(() => {
                        const newOption = new Option(kota, kota, false, false);
                        $('#select_kota_2').append(newOption).trigger('change');
                        $('#input_kota_2').val('').focus();
                        
                        // Kembalikan tombol ke semula
                        resetButtonLoading(btn);
                    }, 800);
                }
            });

            $('#select_kota_2').on('change', function() {
                $('#terpilih_2').text($(this).val() || '-');
            });
        });
    </script>
@endsection