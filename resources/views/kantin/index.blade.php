@extends($layout)

@section('title-page', 'Pemesanan Kantin')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Kantin</li>
@endsection

@section('style-page')
    <style>
        .vendor-section { 
            background: #f8f9fa; 
            padding: 20px; 
            border-radius: 12px; 
            margin-bottom: 30px; 
            border: 1px solid #e9ecef;
        }
        .vendor-title {
            color: #343a40;
            font-weight: bold;
            border-left: 5px solid #9a55ff;
            padding-left: 15px;
            margin-bottom: 20px;
            font-size: 1.25rem;
        }
        .menu-card-inner { 
            transition: transform 0.2s; 
            border: none; 
        }
        .menu-card-inner:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
        }
        .total-price { 
            font-size: 1.8rem; 
            font-weight: bold; 
            color: #9a55ff; 
        }
        .cart-sticky { 
            position: sticky; 
            top: 70px; 
        }
        .vendor-name { 
            font-size: 0.85rem; 
            color: #6c757d; 
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <!-- Kolom Menu -->
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title">Menu Kantin</h4>
                    
                    {{-- Filter Vendor --}}
                    <div class="form-group mb-4">
                        <label for="vendor-filter" class="form-label font-weight-bold">Pilih Vendor:</label>
                        <select id="vendor-filter" class="form-control">
                            <option value="all">Semua Vendor</option>
                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->idvendor }}">{{ $vendor->nama_vendor }}</option>
                            @endforeach
                        </select>
                    </div>
                    <hr>

                    {{-- Menu dipisah per Vendor --}}
                    <div id="menu-cards-container">
                        @foreach($vendors as $vendor)
                            <div class="vendor-section" data-vendor-id="{{ $vendor->idvendor }}">
                                <h5 class="vendor-title">
                                    <i class="mdi mdi-store"></i> {{ $vendor->nama_vendor }}
                                </h5>
                                <div class="row">
                                    @forelse($vendor->menus as $menu)
                                        <div class="col-md-4 mb-4 menu-item-card" data-vendor-id="{{ $vendor->idvendor }}">
                                            <div class="card shadow-sm menu-card-inner h-100">
                                                @if($menu->path_gambar)
                                                    <img src="{{ asset('storage/' . $menu->path_gambar) }}" 
                                                         class="card-img-top" 
                                                         alt="{{ $menu->nama_menu }}" 
                                                         style="height: 140px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light text-center py-4" style="height: 140px;">
                                                        <i class="mdi mdi-food mdi-48px text-muted"></i>
                                                    </div>
                                                @endif
                                                
                                                <div class="card-body p-3 d-flex flex-column">
                                                    <h6 class="font-weight-bold mb-1">{{ $menu->nama_menu }}</h6>
                                                    <h5 class="text-primary font-weight-bold mb-3">
                                                        Rp {{ number_format($menu->harga, 0, ',', '.') }}
                                                    </h5>
                                                    <div class="mb-3">
                                                        <input type="text" 
                                                               id="note-{{ $menu->idmenu }}" 
                                                               class="form-control form-control-sm" 
                                                               placeholder="Catatan: pedas, dll">
                                                    </div>
                                                    <button type="button" 
                                                            class="btn btn-gradient-primary btn-sm btn-block mt-auto"
                                                            onclick="tambahItem({{ $menu->idmenu }}, '{{ addslashes($menu->nama_menu) }}', {{ $menu->harga }}, '{{ addslashes($vendor->nama_vendor) }}', {{ $vendor->idvendor }})">
                                                        <i class="mdi mdi-plus"></i> Tambah
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <p class="text-muted">Belum ada menu untuk vendor ini.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Keranjang -->
        <div class="col-md-4">
            <div class="card shadow-sm cart-sticky border-primary">
                <div class="card-body d-flex flex-column" style="min-height: 500px;">
                    <h4 class="card-title text-primary text-center">
                        <i class="mdi mdi-cart"></i> Keranjang
                    </h4>
                    <hr>
                    <div class="text-center mb-3">
                        <div id="total-text" class="total-price">Rp 0</div>
                    </div>
                    <div id="keranjang-list-container" class="flex-grow-1 overflow-auto mb-3" style="max-height: 400px;">
                        <ul id="keranjang-list" class="list-group list-group-flush">
                            <li class="list-group-item text-center text-muted py-4">Belum ada barang dipilih</li>
                        </ul>
                    </div>
                    <hr>
                    <button type="button" class="btn btn-outline-danger btn-sm mb-2" onclick="batalkanTransaksi()">
                        <i class="mdi mdi-cancel"></i> Batalkan
                    </button>
                    <button id="btn-bayar" class="btn btn-gradient-primary btn-block btn-lg font-weight-bold" 
                            onclick="prosesCheckout()" disabled>
                        <i class="mdi mdi-cash-multiple"></i> BAYAR SEKARANG
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Riwayat Transaksi --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title text-muted">Riwayat Transaksi</h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Waktu</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pesanan ?? [] as $p)
                                    <tr>
                                        <td><small class="font-weight-bold">{{ $p->order_id_pg }}</small></td>
                                        <td>{{ $p->timestamp->format('d M Y, H:i') }}</td>
                                        <td class="text-primary font-weight-bold">
                                            Rp {{ number_format($p->total, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            @if($p->status_bayar == 0)
                                                <span class="badge badge-pending">Pending</span>
                                            @elseif($p->status_bayar == 1)
                                                <span class="badge badge-success">Lunas</span>
                                            @else
                                                <span class="badge badge-failed">Gagal</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($p->status_bayar == 0 && $p->snap_token)
                                                <button class="btn btn-sm btn-primary" 
                                                        onclick="bayarLagi('{{ $p->snap_token }}', {{ $p->idpesanan }})">
                                                    Bayar
                                                </button>
                                            @else 
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada transaksi</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js-page')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

    <script>
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        $(document).ready(function() {
            let keranjang = [];
            let grandTotal = 0;

            // Tambah Item ke Keranjang
            window.tambahItem = function(idmenu, nama_menu, harga, nama_vendor, idvendor) {
                const catatan = $(`#note-${idmenu}`).val().trim() || null;

                const exist = keranjang.find(i => i.idmenu === idmenu && i.catatan === catatan);

                if (exist) {
                    exist.jumlah++;
                    exist.subtotal = exist.jumlah * exist.harga;
                } else {
                    keranjang.push({
                        idmenu: idmenu,
                        nama: nama_menu,
                        harga: harga,
                        jumlah: 1,
                        subtotal: harga,
                        catatan: catatan,
                        nama_vendor: nama_vendor,
                        idvendor: parseInt(idvendor)
                    });
                }

                $(`#note-${idmenu}`).val('');
                renderKeranjang();

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Ditambahkan ke keranjang',
                    showConfirmButton: false,
                    timer: 1500
                });
            };

            // Render Keranjang dengan urutan berdasarkan idvendor
            window.renderKeranjang = function() {
                // Urutkan keranjang berdasarkan idvendor
                keranjang.sort((a, b) => a.idvendor - b.idvendor);

                let html = '';
                grandTotal = 0;

                if (keranjang.length === 0) {
                    html = '<li class="list-group-item text-center text-muted py-4">Belum ada barang dipilih</li>';
                } else {
                    keranjang.forEach((item, index) => {
                        grandTotal += item.subtotal;
                        html += `
                            <li class="list-group-item px-0 border-bottom">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div style="width: 65%">
                                        <small class="vendor-name">${item.nama_vendor}</small>
                                        <span class="font-weight-bold d-block">${item.nama}</span>
                                        ${item.catatan ? `<small class="text-info">"${item.catatan}"</small>` : ''}
                                        <small class="text-muted">Rp ${item.harga.toLocaleString('id-ID')}</small>
                                    </div>
                                    <div class="text-center" style="width: 20%">
                                        <button class="btn btn-xs btn-outline-secondary p-1" onclick="updateQty(${index}, -1)">−</button>
                                        <span class="mx-2 font-weight-bold">${item.jumlah}</span>
                                        <button class="btn btn-xs btn-outline-secondary p-1" onclick="updateQty(${index}, 1)">+</button>
                                    </div>
                                    <div class="text-end" style="width: 25%">
                                        <span class="d-block font-weight-bold">Rp ${item.subtotal.toLocaleString('id-ID')}</span>
                                        <button class="btn btn-link text-danger p-0 small" onclick="hapusItem(${index})">Hapus</button>
                                    </div>
                                </div>
                            </li>`;
                    });
                }

                $('#keranjang-list').html(html);
                $('#total-text').text('Rp ' + grandTotal.toLocaleString('id-ID'));
                $('#btn-bayar').prop('disabled', keranjang.length === 0);
            };

            window.updateQty = function(index, delta) {
                const item = keranjang[index];
                item.jumlah += delta;
                if (item.jumlah < 1) {
                    hapusItem(index);
                } else {
                    item.subtotal = item.jumlah * item.harga;
                    renderKeranjang();
                }
            };

            window.hapusItem = function(index) {
                keranjang.splice(index, 1);
                renderKeranjang();
            };

            window.batalkanTransaksi = function() {
                if (keranjang.length === 0) return;
                Swal.fire({
                    title: 'Kosongkan keranjang?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, kosongkan'
                }).then((result) => {
                    if (result.isConfirmed) {
                        keranjang = [];
                        renderKeranjang();
                    }
                });
            };

            window.prosesCheckout = function() {
                const btn = $('#btn-bayar');
                btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Memproses...');

                axios.post("{{ route('kantin.checkout') }}", {
                    total_bayar: grandTotal,
                    cart: keranjang
                })
                .then(function(res) {
                    window.snap.pay(res.data.snap_token, {
                        onSuccess: function() {
                            window.location.href = "/kantin/selesai/" + res.data.idpesanan;
                        },
                        onPending: function() {
                            window.location.href = "{{ route('kantin.pending') }}";
                        },
                        onClose: function() {
                            btn.prop('disabled', false).html('<i class="mdi mdi-cash-multiple"></i> BAYAR SEKARANG');
                        }
                    });
                })
                .catch(function(error) {
                    const xhr = error.response;
                    Swal.fire('Gagal', (xhr && xhr.data && xhr.data.error) || 'Terjadi kesalahan', 'error');
                    btn.prop('disabled', false).html('<i class="mdi mdi-cash-multiple"></i> BAYAR SEKARANG');
                });
            };

            // Filter Vendor
            $('#vendor-filter').on('change', function() {
                const v = $(this).val();
                if (v === 'all') {
                    $('.menu-item-card').fadeIn();
                } else {
                    $('.menu-item-card').hide();
                    $(`.menu-item-card[data-vendor-id="${v}"]`).fadeIn();
                }
            });
        });

        function bayarLagi(token, id) {
            window.snap.pay(token, {
                onSuccess: function() {
                    window.location.href = "/kantin/selesai/" + id;
                }
            });
        }
    </script>
@endsection