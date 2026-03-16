@extends('layouts.admin.main')

@section('title-page', 'Tambah Barang (Local - DataTables)')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Barang Baru - DataTables</li>
@endsection

@section('css-page')
    <!-- CSS DataTables Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.bootstrap5.min.css">
@endsection

@section('content')
    <style>
        /* Efek hover agar row terlihat clickable */
        #tableBarang tbody tr {
            cursor: pointer;
            transition: background-color 0.15s;
        }

        #tableBarang tbody tr:hover {
            background-color: #f8f9fa;
        }
    </style>

    <div class="row">
        <!-- Card Form Tambah Barang -->
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Form Tambah Barang Baru (Lokal - Datatable)</h5>
                    <form id="formBarang">
                        <div class="form-group mb-3">
                            <label>Nama Barang :</label>
                            <input type="text" id="nama_barang" class="form-control" required
                                placeholder="Masukkan nama barang">
                        </div>
                        <div class="form-group mb-3">
                            <label>Harga Barang :</label>
                            <input type="number" id="harga_barang" class="form-control" required min="1"
                                placeholder="Masukkan harga (tanpa titik/koma)">
                        </div>
                        <div class="text-end">
                            <button type="button" id="btnSubmit" class="btn btn-success px-4">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Card Tabel Daftar Barang -->
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Daftar Barang (DataTables - Lokal)</h5>
                    <div class="table-responsive">
                        <table id="tableBarang" class="table table-bordered table-hover" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>ID Barang</th>
                                    <th>Nama</th>
                                    <th>Harga</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit & Hapus Barang -->
    <div class="modal fade" id="modalBarang" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Detail & Ubah Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="formModal">
                        <div class="mb-3">
                            <label class="form-label">ID Barang :</label>
                            <input type="text" id="modal_id" class="form-control" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Barang :</label>
                            <input type="text" id="modal_nama" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Harga Barang :</label>
                            <input type="number" id="modal_harga" class="form-control" required min="1">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnHapus" class="btn btn-danger px-4">Hapus</button>
                    <button type="button" id="btnUbah" class="btn btn-success px-4">Ubah</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js-page')
    <!-- JS DataTables -->
    <script src="https://cdn.datatables.net/2.3.7/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // =============================================
        // Inisialisasi DataTable dengan fitur standar
        // =============================================
        const table = $('#tableBarang').DataTable({
            paging: true,           // pagination
            searching: true,        // search box
            ordering: true,         // sorting kolom
            info: true,             // info "Showing 1 to 10 of 50 entries"
            language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' } // bahasa Indonesia
        });

        // =============================================
        // Fungsi loader untuk semua tombol
        // =============================================
        function setButtonLoading(btn, text = 'Memproses...') {
            btn.disabled = true;
            btn.setAttribute('data-original-text', btn.innerHTML);
            
            // Buat isi tombol menjadi spinner + teks
            btn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    ${text}
                `;
        }

        function resetButtonLoading(btn) {
            btn.disabled = false;
            const original = btn.getAttribute('data-original-text') || 'Submit';
            btn.innerHTML = original;
        }

        // =============================================
        // Format harga ke Rupiah
        // =============================================
        function formatRp(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(value);
        }

        let currentRowIndex = null;

        // =============================================
        // Tambah Barang Baru
        // =============================================
        document.getElementById('btnSubmit').addEventListener('click', function () {
            const form = document.getElementById('formBarang');
            const btn = this;

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            setButtonLoading(btn, 'Menyimpan...');

            setTimeout(() => {
                const nama = document.getElementById('nama_barang').value.trim();
                const harga = document.getElementById('harga_barang').value;
                const id = 'BRG-' + Date.now().toString().slice(-6);

                // Tambah row ke DataTable
                table.row.add([id, nama, formatRp(harga)]).draw(false);

                // Simpan harga asli (raw) di dataset node
                const lastIdx = table.rows().count() - 1;
                const addedNode = table.row(lastIdx).node();
                addedNode.dataset.rawHarga = harga;

                form.reset();
                resetButtonLoading(btn);
            }, 600); // delay agar loader terlihat
        });

        // =============================================
        // Klik row tabel → buka modal
        // =============================================
        $('#tableBarang tbody').on('click', 'tr', function () {
            const row = table.row(this);
            const data = row.data();
            const node = row.node();

            if (data) {
                $('#modal_id').val(data[0]);
                $('#modal_nama').val(data[1]);
                $('#modal_harga').val(node.dataset.rawHarga || data[2].replace(/[^0-9]/g, ''));

                currentRowIndex = row.index();

                const modal = new bootstrap.Modal(document.getElementById('modalBarang'));
                modal.show();
            }
        });

        // =============================================
        // Tombol Ubah di modal
        // =============================================
        document.getElementById('btnUbah').addEventListener('click', function () {
            const form = document.getElementById('formModal');
            const btn = this;

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            setButtonLoading(btn, 'Mengubah...');

            setTimeout(() => {
                const namaBaru = document.getElementById('modal_nama').value.trim();
                const hargaBaru = document.getElementById('modal_harga').value;

                if (currentRowIndex !== null) {
                    const row = table.row(currentRowIndex);
                    row.data([row.data()[0], namaBaru, formatRp(hargaBaru)]).draw(false);
                    row.node().dataset.rawHarga = hargaBaru;
                }

                bootstrap.Modal.getInstance(document.getElementById('modalBarang')).hide();
                resetButtonLoading(btn);
            }, 600);
        });

        // =============================================
        // Tombol Hapus di modal
        // =============================================
        document.getElementById('btnHapus').addEventListener('click', function () {
            const btn = this;

            if (!confirm('Yakin ingin menghapus barang ini?')) return;

            setButtonLoading(btn, 'Menghapus...');

            setTimeout(() => {
                if (currentRowIndex !== null) {
                    table.row(currentRowIndex).remove().draw();
                }
                bootstrap.Modal.getInstance(document.getElementById('modalBarang')).hide();
                resetButtonLoading(btn);
            }, 500);
        });
    </script>
@endsection