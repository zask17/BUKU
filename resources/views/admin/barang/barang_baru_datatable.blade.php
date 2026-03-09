@extends('layouts.admin.main')

@section('title-page', 'Tambah Barang (Local - DataTables)')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Barang Baru - DataTables</li>
@endsection

@section('css-page')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.bootstrap5.min.css">
@endsection

@section('content')
<style>
    #tableBarang tbody tr {
        cursor: pointer;
        transition: background-color 0.15s;
    }
    #tableBarang tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <form id="formBarang">
                    <div class="form-group">
                        <label>Nama Barang :</label>
                        <input type="text" id="nama_barang" class="form-control" required placeholder="Masukkan nama barang">
                    </div>
                    <div class="form-group">
                        <label>Harga Barang :</label>
                        <input type="number" id="harga_barang" class="form-control" required min="1" placeholder="Masukkan harga">
                    </div>
                    <div class="text-end">
                        <button type="button" id="btnSubmit" class="btn btn-success">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Barang</h4>
                <div class="table-responsive">
                    <table id="tableBarang" class="table table-bordered table-hover" style="width:100%">
                        <thead>
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

<!-- Modal sama seperti di atas -->
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
                <button type="button" id="btnHapus" class="btn btn-danger">Hapus</button>
                <button type="button" id="btnUbah" class="btn btn-success">Ubah</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-page')
    <script src="https://cdn.datatables.net/2.3.7/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.7/js/dataTables.bootstrap5.min.js"></script>

<script>
const table = $('#tableBarang').DataTable({
    paging: true,
    searching: true,
    ordering: true,
    info: true,
    language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' }
});

function formatRp(value) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
}

let currentRowIndex = null;

// Tambah data
document.getElementById('btnSubmit').addEventListener('click', function () {
    const form = document.getElementById('formBarang');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    // setButtonLoading(this, 'Menyimpan...');

    const nama = document.getElementById('nama_barang').value.trim();
    const harga = document.getElementById('harga_barang').value;
    const id = 'BRG-' + Date.now().toString().slice(-6);

    table.row.add([id, nama, formatRp(harga)]).draw(false);

    // Simpan raw harga di node
    const addedNode = table.row(table.rows().count() - 1).node();
    addedNode.dataset.rawHarga = harga;

    form.reset();
    // resetButtonLoading(this);
});

// Klik row
$('#tableBarang tbody').on('click', 'tr', function () {
    if ($(this).hasClass('selected')) {
        $(this).removeClass('selected');
    } else {
        table.$('tr.selected').removeClass('selected');
        $(this).addClass('selected');

        const data = table.row(this).data();
        const node = table.row(this).node();

        $('#modal_id').val(data[0]);
        $('#modal_nama').val(data[1]);
        $('#modal_harga').val(node.dataset.rawHarga || data[2].replace(/[^0-9]/g, ''));

        currentRowIndex = table.row(this).index();

        const modal = new bootstrap.Modal($('#modalBarang')[0]);
        modal.show();
    }
});

// Ubah
$('#btnUbah').on('click', function () {
    const form = document.getElementById('formModal');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const namaBaru = $('#modal_nama').val().trim();
    const hargaBaru = $('#modal_harga').val();

    if (currentRowIndex !== null) {
        const row = table.row(currentRowIndex);
        const node = row.node();

        row.data([row.data()[0], namaBaru, formatRp(hargaBaru)]).draw(false);
        node.dataset.rawHarga = hargaBaru;
    }

    bootstrap.Modal.getInstance($('#modalBarang')[0]).hide();
});

// Hapus
$('#btnHapus').on('click', function () {
    if (confirm('Yakin hapus barang ini?')) {
        if (currentRowIndex !== null) {
            table.row(currentRowIndex).remove().draw();
        }
        bootstrap.Modal.getInstance($('#modalBarang')[0]).hide();
    }
});
</script>
@endsection