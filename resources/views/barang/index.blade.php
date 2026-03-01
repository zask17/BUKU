@extends($layout)

@section('title-page', 'Manajemen Tag Harga')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Daftar Barang</li>
@endsection

@section('style-page')
    <style>
        .badge-gradient-info {
            background: linear-gradient(to right, #36d1dc, #5b86e5);
            color: white;
        }

        .config-print {
            background: #f8f9fa;
            border-radius: 10px;
            border: 1px solid #e9ecef;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title"> Harga Barang </h3>
        </div>

        <div class="row">
            {{-- KALAU GAK PILIH BARANG APA-APA --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-alert-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <a href="{{ route('barang.create') }}" class="btn btn-primary mb-3">+ Tambah Barang</a>

                        <form id="formCetak" action="{{ route('barang.cetak') }}" method="POST">
                            @csrf
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="barangTable">
                                    <thead>
                                        <tr>
                                            <th> <input type="checkbox" id="checkAll"> </th>
                                            <th> ID Barang </th>
                                            <th> Nama Barang </th>
                                            <th> Harga </th>
                                            <th> Waktu Ditambahkan </th>
                                            <th> Aksi </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($barangs as $b)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="selected_items[]" value="{{ $b->id_barang }}"
                                                        class="item-checkbox">
                                                </td>
                                                <td><code>{{ $b->id_barang }}</code></td>
                                                <td>{{ $b->nama }}</td>
                                                <td>Rp {{ number_format($b->harga, 0, ',', '.') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($b->timestamp)->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <a href="{{ route('barang.edit', $b->id_barang) }}"
                                                        class="btn btn-sm btn-warning">Edit</a>

                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="if(confirm('Hapus barang ini?')) { document.getElementById('delete-form-{{ $b->id_barang }}').submit(); }">
                                                        Hapus
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4 p-4 config-print">
                                <h5 class="text-primary mb-3"><i class="mdi mdi-printer"></i> Konfigurasi Label TnJ 108</h5>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="font-weight-bold">Mulai Baris (Y):</label>
                                        <input type="number" name="startY" class="form-control" min="1" max="8" value="1"
                                            required>
                                        <small class="text-muted text-small">Max baris: 8</small>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="font-weight-bold">Mulai Kolom (X):</label>
                                        <input type="number" name="startX" class="form-control" min="1" max="5" value="1"
                                            required>
                                        <small class="text-muted text-small">Max kolom: 5</small>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="submit" class="btn btn-success btn-icon-text">
                                            <i class="mdi mdi-file-pdf btn-icon-prepend"></i> Cetak Label PDF
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach($barangs as $b)
        <form id="delete-form-{{ $b->id_barang }}" action="{{ route('barang.destroy', $b->id_barang) }}" method="POST"
            style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // 1. Inisialisasi DataTable (Source 7)
            var table = $('#barangTable').DataTable({
                "pageLength": 10
            });

            // 2. Logika Check All yang bekerja di seluruh halaman (Source 9)
            $('#checkAll').on('click', function () {
                var isChecked = this.checked;
                // Mencari semua checkbox di seluruh baris tabel (termasuk yang tidak terlihat/paginasi)
                $(table.rows().nodes()).find('.item-checkbox').prop('checked', isChecked);
            });

            // 3. Update status Master Checkbox jika salah satu item di-uncheck manual
            $('#barangTable tbody').on('change', '.item-checkbox', function () {
                if (!this.checked) {
                    $('#checkAll').prop('checked', false);
                }
            });
        });

        $('#formCetak').on('submit', function (e) {

            var checkedItems = $('.item-checkbox:checked').length;

            if (checkedItems === 0) {
                e.preventDefault(); // Stop submit

                alert('Pilih minimal satu barang untuk dicetak!');

                return false;
            }

            // Kalau ada yang dipilih → buka tab baru
            $(this).attr('target', '_blank');

        });
    </script>
@endsection