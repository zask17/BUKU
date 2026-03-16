@extends($layout)

@section('title-page', 'POS - Versi AJAX')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">POS - AJAX</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title">Input Kasir (AJAX)</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kode Barang (Enter/Pilih):</label>
                            <input type="text" id="kode_input" class="form-control" list="barang_list" autocomplete="off" onkeypress="checkBarangEnter(event)" oninput="checkBarangAutomatis()">
                            <datalist id="barang_list">
                                @foreach($barangs as $b)
                                    <option value="{{ $b->id_barang }}">{{ $b->nama }} - Rp {{ number_format($b->harga) }}</option>
                                @endforeach
                            </datalist>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Jumlah:</label>
                            <input type="number" id="qty_input" class="form-control" value="1" min="1" oninput="toggleAddBtn()">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Barang:</label>
                            <input type="text" id="nama_barang" class="form-control" readonly style="background: #eee;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Harga Barang:</label>
                            <input type="text" id="harga_barang" class="form-control" readonly style="background: #eee;">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-success btn-block" id="btn-tambah" onclick="addKeTabel()" disabled>
                    <i class="mdi mdi-plus"></i> Tambahkan ke Tabel
                </button>

                <div class="table-responsive mt-4">
                    <table class="table table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th>Barang</th><th>Harga</th><th style="width: 80px;">Qty</th><th>Subtotal</th><th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="keranjang-body">
                            <tr><td colspan="5" class="text-center text-muted">Belum ada barang di keranjang</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 grid-margin stretch-card">
        <div class="card shadow-sm border-info text-center">
            <div class="card-body">
                <h4 class="card-title">Ringkasan Transaksi</h4>
                <hr>
                <div class="display-4 text-info font-weight-bold mb-4" id="total-display">Rp 0</div>
                <button type="button" class="btn btn-outline-danger btn-sm btn-block mb-3" onclick="batalkanTransaksi()">Batalkan</button>
                <button type="button" class="btn btn-info btn-lg btn-block" id="btn-bayar" onclick="simpanTransaksi(this)" disabled>PROSES BAYAR</button>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title text-muted">Riwayat Transaksi Detail</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="bg-light">
                            <tr style="text-align: center;">
                                <th>ID</th>
                                <th>Waktu</th>
                                <th>Kasir</th>
                                <th>Barang</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                                <th>Total Transaksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Mengelompokkan riwayat berdasarkan id_penjualan untuk menghitung rowspan
                                $groupedRiwayat = $riwayat->groupBy('id_penjualan');
                            @endphp

                            @foreach($groupedRiwayat as $idPenjualan => $items)
                                @foreach($items as $index => $r)
                                <tr>
                                    {{-- Kolom yang di-merge berdasarkan ID --}}
                                    @if($index === 0)
                                        <td rowspan="{{ $items->count() }}">#{{ $r->id_penjualan }}</td>
                                        <td rowspan="{{ $items->count() }}">{{ date('d/m/Y H:i', strtotime($r->timestamp)) }}</td>
                                        <td rowspan="{{ $items->count() }}">{{ $r->kasir }}</td>
                                    @endif

                                    {{-- Kolom detail barang (tidak di-merge) --}}
                                    <td>{{ $r->nama_barang }}</td>
                                    <td>{{ $r->jumlah }}</td>
                                    <td>Rp {{ number_format($r->subtotal, 0, ',', '.') }}</td>

                                    {{-- Kolom Total yang di-merge --}}
                                    @if($index === 0)
                                        <td rowspan="{{ $items->count() }}" class="font-weight-bold text-primary" style="vertical-align: middle;">
                                            Rp {{ number_format($r->total_transaksi, 0, ',', '.') }}
                                        </td>
                                    @endif
                                </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-page')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let cart = [];
    let activeItem = null;

    function checkBarangAutomatis() {
        const kode = $('#kode_input').val();
        if (kode.length >= 8) fetchBarang(kode);
    }

    function checkBarangEnter(e) {
        if (e.key === 'Enter') fetchBarang($('#kode_input').val());
    }

    function fetchBarang(kode) {
        $.post("{{ route('admin.pos.cek_barang') }}", { kode: kode, _token: "{{ csrf_token() }}" })
            .done(res => {
                activeItem = res.data;
                $('#nama_barang').val(activeItem.nama);
                $('#harga_barang').val(activeItem.harga);
                toggleAddBtn();
            });
    }

    function toggleAddBtn() {
        const qty = $('#qty_input').val();
        $('#btn-tambah').prop('disabled', !(activeItem && qty > 0));
    }

    function addKeTabel() {
        const qty = parseInt($('#qty_input').val());
        const exist = cart.find(i => i.id_barang === activeItem.id_barang);
        if (exist) {
            exist.qty += qty;
            exist.subtotal = exist.qty * exist.harga;
        } else {
            cart.push({ ...activeItem, qty: qty, subtotal: qty * activeItem.harga });
        }
        renderCart();
        resetForm();
    }

    function renderCart() {
        let h = ''; let total = 0;
        cart.forEach((item, i) => {
            total += item.subtotal;
            h += `<tr><td>${item.nama}</td><td>Rp ${item.harga.toLocaleString('id-ID')}</td>
                <td><input type="number" value="${item.qty}" class="form-control form-control-sm" onchange="updateQty(${i}, this.value)" min="1"></td>
                <td class="font-weight-bold">Rp ${item.subtotal.toLocaleString('id-ID')}</td>
                <td><button class="btn btn-danger btn-sm" onclick="hapusItem(${i})">X</button></td></tr>`;
        });
        $('#keranjang-body').html(h || '<tr><td colspan="5" class="text-center text-muted">Belum ada barang di keranjang</td></tr>');
        $('#total-display').text('Rp ' + total.toLocaleString('id-ID'));
        $('#btn-bayar').prop('disabled', cart.length === 0);
    }

    function updateQty(i, val) {
        cart[i].qty = parseInt(val) || 1;
        cart[i].subtotal = cart[i].qty * cart[i].harga;
        renderCart();
    }

    function hapusItem(i) { cart.splice(i, 1); renderCart(); }

    function resetForm() {
        activeItem = null;
        $('#kode_input').val("");
        $('#nama_barang').val("");
        $('#harga_barang').val("");
        toggleAddBtn();
    }

    function batalkanTransaksi() {
        if (cart.length === 0) return;
        Swal.fire({ title: 'Batalkan Transaksi?', icon: 'warning', showCancelButton: true }).then((r) => {
            if (r.isConfirmed) { cart = []; renderCart(); }
        });
    }

    function simpanTransaksi(btn) {
        setButtonLoading(btn, 'Proses...');
        $.ajax({
            url: "{{ route('admin.pos.store') }}",
            type: "POST",
            data: { 
                items: cart, 
                total_harga: cart.reduce((a, b) => a + b.subtotal, 0), 
                _token: "{{ csrf_token() }}" 
            },
            success: function(res) {
                Swal.fire('Berhasil!', res.msg, 'success').then(() => location.reload());
            },
            error: function() {
                Swal.fire('Gagal', 'Terjadi kesalahan sistem', 'error');
            },
            complete: function() { resetButtonLoading(btn); }
        });
    }
</script>
@endsection