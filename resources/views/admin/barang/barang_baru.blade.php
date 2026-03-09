@extends('layouts.admin.main')

@section('title-page', 'Tambah Barang')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Tambah Barang</li>
@endsection

@section('content')
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
                <h4 class="card-title">Daftar Barang (View Only)</h4>
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
@endsection

@section('js-page')
<script>
    // CREATE: Tambah Barang (Hanya View)
    document.getElementById('btnSubmit').addEventListener('click', function () {
        const form = document.getElementById('formBarang');
        const btn = this;

        if (!form.checkValidity()) { 
            form.reportValidity(); 
            return; 
        }

        // Loader
        setButtonLoading(btn, 'Menyimpan...');

        setTimeout(() => {
            const nama = document.getElementById('nama_barang').value;
            const harga = document.getElementById('harga_barang').value;
            const idRandom = 'BRG-' + Math.floor(Math.random() * 10000);

            const row = `<tr>
                <td>${idRandom}</td>
                <td>${nama}</td>
                <td>${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(harga)}</td>
            </tr>`;
            
            document.querySelector('#tableBarang tbody').insertAdjacentHTML('beforeend', row);
            
            // Reset Form
            form.reset();
            resetButtonLoading(btn);
        }, 800);
    });
</script>
@endsection