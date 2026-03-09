@extends('layouts.admin.main')

@section('title-page', 'Tambah Barang (Local - HTML Table)')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Barang Baru - HTML</li>
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
                    <table class="table table-bordered table-hover" id="tableBarang">
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

<!-- Modal Bootstrap 5 -->
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
<script>
// Format harga helper
function formatRp(value) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
}

let currentRow = null;

// Tambah barang
document.getElementById('btnSubmit').addEventListener('click', function () {
    const form = document.getElementById('formBarang');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    // setButtonLoading(this, 'Menyimpan...'); // uncomment jika ada fungsi global

    const nama = document.getElementById('nama_barang').value.trim();
    const harga = document.getElementById('harga_barang').value;
    const id = 'BRG-' + Date.now().toString().slice(-6);

    const tbody = document.querySelector('#tableBarang tbody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>${id}</td>
        <td>${nama}</td>
        <td data-raw="${harga}">${formatRp(harga)}</td>
    `;
    tbody.appendChild(tr);

    form.reset();
    // resetButtonLoading(this);
});

// Klik row → modal
document.addEventListener('click', function(e) {
    const tr = e.target.closest('#tableBarang tbody tr');
    if (!tr) return;

    currentRow = tr;

    const id    = tr.cells[0].textContent;
    const nama  = tr.cells[1].textContent;
    const harga = tr.cells[2].dataset.raw;

    document.getElementById('modal_id').value    = id;
    document.getElementById('modal_nama').value  = nama;
    document.getElementById('modal_harga').value = harga;

    const modal = new bootstrap.Modal(document.getElementById('modalBarang'));
    modal.show();
});

// Ubah
document.getElementById('btnUbah').addEventListener('click', function() {
    const form = document.getElementById('formModal');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const namaBaru  = document.getElementById('modal_nama').value.trim();
    const hargaBaru = document.getElementById('modal_harga').value;

    if (currentRow) {
        currentRow.cells[1].textContent = namaBaru;
        currentRow.cells[2].dataset.raw = hargaBaru;
        currentRow.cells[2].textContent = formatRp(hargaBaru);
    }

    bootstrap.Modal.getInstance(document.getElementById('modalBarang')).hide();
});

// Hapus
document.getElementById('btnHapus').addEventListener('click', function() {
    if (confirm('Yakin ingin menghapus barang ini?')) {
        if (currentRow) currentRow.remove();
        bootstrap.Modal.getInstance(document.getElementById('modalBarang')).hide();
    }
});
</script>
@endsection