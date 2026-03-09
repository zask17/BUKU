@extends('layouts.admin.main')

@section('title-page', 'Tambah Barang (Datable)')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Tambah Barang Datable</li>
@endsection

@section('content')
<style>
    /* Hover pointer pada row */
    #tableBarang tbody tr {
        cursor: pointer;
        transition: background-color 0.2s;
    }
    #tableBarang tbody tr:hover {
        background-color: #f1f1f1;
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
                        <input type="number" id="harga_barang" class="form-control" required placeholder="Masukkan harga">
                    </div>
                    <button type="button" id="btnSubmit" class="btn btn-success float-right">Submit</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Barang (HTML Table)</h4>
                <div class="table-responsive">
                    <table class="table table-bordered" id="tableBarang">
                        <thead>
                            <tr>
                                <th>ID Barang</th>
                                <th>Nama</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalBarang" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail / Ubah Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formModal">
                    <div class="form-group">
                        <label>ID Barang :</label>
                        <input type="text" id="modal_id" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Nama Barang :</label>
                        <input type="text" id="modal_nama" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Harga Barang :</label>
                        <input type="number" id="modal_harga" class="form-control" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnHapus" class="btn btn-danger">Hapus</button>
                <button type="button" id="btnUbah" class="btn btn-primary">Ubah</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-page')
<script>
    let selectedRow = null;

    // CREATE: Tambah Barang
    document.getElementById('btnSubmit').addEventListener('click', function () {
        const form = document.getElementById('formBarang');
        const btn = this;
        const originalText = btn.innerHTML;

        if (!form.checkValidity()) { form.reportValidity(); return; }

        // Ketentuan: Loader
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Menyimpan...`;

        setTimeout(() => {
            const nama = document.getElementById('nama_barang').value;
            const harga = document.getElementById('harga_barang').value;
            const idRandom = 'BRG-' + Math.floor(Math.random() * 10000);

            const row = `<tr>
                <td>${idRandom}</td>
                <td>${nama}</td>
                <td data-raw="${harga}">${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(harga)}</td>
            </tr>`;
            
            document.querySelector('#tableBarang tbody').insertAdjacentHTML('beforeend', row);
            
            // Ketentuan: Input menjadi kosong
            form.reset();
            btn.disabled = false;
            btn.innerHTML = originalText;
        }, 800);
    });

    // READ: Klik row untuk buka modal
    $(document).on('click', '#tableBarang tbody tr', function() {
        selectedRow = $(this);
        const id = selectedRow.find('td:eq(0)').text();
        const nama = selectedRow.find('td:eq(1)').text();
        const harga = selectedRow.find('td:eq(2)').attr('data-raw');

        $('#modal_id').val(id);
        $('#modal_nama').val(nama);
        $('#modal_harga').val(harga);
        $('#modalBarang').modal('show');
    });

    // UPDATE: Ubah data di row
    $('#btnUbah').on('click', function() {
        const btn = $(this);
        if (!document.getElementById('formModal').checkValidity()) { 
            document.getElementById('formModal').reportValidity(); 
            return; 
        }

        btn.prop('disabled', true).html(`<span class="spinner-border spinner-border-sm"></span> Loading...`);

        setTimeout(() => {
            const newNama = $('#modal_nama').val();
            const newHarga = $('#modal_harga').val();

            selectedRow.find('td:eq(1)').text(newNama);
            selectedRow.find('td:eq(2)')
                .attr('data-raw', newHarga)
                .text(new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(newHarga));

            $('#modalBarang').modal('hide'); // Ketentuan: Tutup modal
            btn.prop('disabled', false).html('Ubah');
        }, 800);
    });

    // DELETE: Hapus row
    $('#btnHapus').on('click', function() {
        const btn = $(this);
        btn.prop('disabled', true).html(`<span class="spinner-border spinner-border-sm"></span> Loading...`);

        setTimeout(() => {
            selectedRow.remove();
            $('#modalBarang').modal('hide'); // Ketentuan: Tutup modal
            btn.prop('disabled', false).html('Hapus');
        }, 800);
    });
</script>
@endsection