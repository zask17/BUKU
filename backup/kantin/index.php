@extends('layouts.guest.main')

@section('title-page', 'Pemesanan Kantin')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Kantin</li>
@endsection

@section('style-page')
    <style>
        .menu-card { cursor: pointer; transition: transform 0.15s, box-shadow 0.15s; }
        .menu-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.12) !important; }
        .cart-sticky { position: sticky; top: 70px; }
        #cart-items { max-height: 420px; overflow-y: auto; }
        .qty-btn { width: 28px; height: 28px; padding: 0; line-height: 1; font-size: 16px; }
        .total-price { font-size: 1.4rem; font-weight: bold; color: #9a55ff; }
        .vendor-divider { border-left: 4px solid #9a55ff; padding-left: 12px; }
        .badge-gradient-info {
            background: linear-gradient(to right, #36d1dc, #5b86e5);
            color: white;
        }
    </style>
@endsection

@section('content')
<div class="row">

    {{-- ── PANEL KIRI: Daftar Menu ── --}}
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Daftar Menu Kantin</h4>
                    <div class="input-group" style="max-width: 240px;">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0">
                                <i class="mdi mdi-magnify text-muted"></i>
                            </span>
                        </div>
                        <input type="text" id="search-input"
                               class="form-control border-left-0"
                               placeholder="Cari menu..."
                               oninput="filterMenu()">
                    </div>
                </div>
                <hr>

                <div id="menu-container">
                    @foreach($vendors as $vendor)
                    <div class="vendor-block mb-4"
                         data-vendor="{{ strtolower($vendor->nama_vendor) }}">

                        <div class="vendor-divider mb-3">
                            <h6 class="font-weight-bold text-uppercase text-muted mb-0">
                                <i class="mdi mdi-store mr-1"></i>{{ $vendor->nama_vendor }}
                            </h6>
                        </div>

                        <div class="row">
                            @forelse($vendor->menus as $menu)
                            <div class="col-sm-6 col-lg-4 mb-3 menu-item-wrap"
                                 data-search="{{ strtolower($menu->nama_menu) }} {{ strtolower($vendor->nama_vendor) }}">
                                <!-- <div class="card menu-card h-100 shadow-sm border-0"
                                     onclick="openNoteModal(
                                         {{ $menu->idmenu }},
                                         '{{ addslashes($menu->nama_menu) }}',
                                         {{ $menu->harga }},
                                         '{{ addslashes($vendor->nama_vendor) }}'
                                     )">
                                    <div class="card-body p-3 d-flex flex-column justify-content-between"> -->
                                        <div>
                                            <h6 class="font-weight-bold mb-1">{{ $menu->nama_menu }}</h6>
                                            <p class="text-primary font-weight-bold mb-2">
                                                Rp {{ number_format($menu->harga, 0, ',', '.') }}
                                            </p>
                                        </div>
                                        <div>
                                            <label class="badge badge-gradient-info mb-0">
                                                {{ $vendor->nama_vendor }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-12">
                                <p class="text-muted text-center py-2 mb-0">Menu belum tersedia.</p>
                            </div>
                            @endforelse
                        </div>

                    </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>

    {{-- ── PANEL KANAN: Keranjang ── --}}
    <div class="col-md-4">
        <div class="card cart-sticky">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-cart-outline mr-1 text-primary"></i> Keranjang
                    </h4>
                    <button class="btn btn-sm btn-outline-danger" onclick="clearCart()">
                        <i class="mdi mdi-trash-can-outline"></i> Kosongkan
                    </button>
                </div>
                <hr>

                <div id="cart-items">
                    <div id="cart-empty" class="text-center text-muted py-4">
                        <i class="mdi mdi-cart-off" style="font-size: 40px; display: block;"></i>
                        <small class="mt-2 d-block">Belum ada item dipilih</small>
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center">
                    <span class="font-weight-bold">Total</span>
                    <div class="text-right">
                        <div id="total-amount" class="total-price">Rp 0</div>
                        <small class="text-muted" id="total-items-text">0 item</small>
                    </div>
                </div>

                <button id="btn-checkout"
                        class="btn btn-gradient-primary btn-block btn-lg font-weight-bold mt-3"
                        onclick="prosesCheckout()"
                        disabled>
                    BAYAR SEKARANG
                </button>

            </div>
        </div>
    </div>

</div>

{{-- ── Modal Catatan ── --}}
<div class="modal fade" id="noteModal" tabindex="-1" role="dialog"
     aria-labelledby="noteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="noteModalLabel">
                    <i class="mdi mdi-plus-circle-outline mr-1 text-primary"></i>
                    Tambah ke Keranjang
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-primary py-2 mb-3">
                    <div class="font-weight-bold" id="modal-menu-name"></div>
                    <small id="modal-menu-detail" class="d-block mt-1"></small>
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold small">
                        Catatan <span class="text-muted font-weight-normal">(opsional)</span>
                    </label>
                    <textarea id="modal-note" class="form-control" rows="2"
                              placeholder="Contoh: Pedas, tanpa bawang..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-gradient-primary font-weight-bold"
                        onclick="tambahDariModal()">
                    <i class="mdi mdi-plus"></i> Tambah
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-page')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
    let keranjang  = [];
    let grandTotal = 0;
    let modalMenu  = null;

    /* ─── Modal Catatan ─── */
    function openNoteModal(id, nama, harga, vendor) {
        modalMenu = { idmenu: id, nama, harga };
        $('#modal-menu-name').text(nama);
        $('#modal-menu-detail').text('Rp ' + harga.toLocaleString('id-ID') + ' · ' + vendor);
        $('#modal-note').val('');
        $('#noteModal').modal('show');
        // Fokus ke textarea setelah modal terbuka
        $('#noteModal').one('shown.bs.modal', function () {
            $('#modal-note').focus();
        });
    }

    function tambahDariModal() {
        if (!modalMenu) return;
        const catatan = $('#modal-note').val().trim() || null;
        tambahItem(modalMenu.idmenu, modalMenu.nama, modalMenu.harga, catatan);
        $('#noteModal').modal('hide');
    }

    // Enter di textarea = langsung tambah
    $('#modal-note').on('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            tambahDariModal();
        }
    });

    /* ─── Logic Keranjang ─── */
    function tambahItem(id, nama, harga, catatan) {
        // Gabung jika idmenu + catatan sama
        const exist = keranjang.find(i => i.idmenu === id && i.catatan === catatan);
        if (exist) {
            exist.jumlah++;
            exist.subtotal = exist.jumlah * exist.harga;
        } else {
            keranjang.push({ idmenu: id, nama, harga, jumlah: 1, subtotal: harga, catatan });
        }
        renderCart();
    }

    function ubahJumlah(index, delta) {
        keranjang[index].jumlah += delta;
        if (keranjang[index].jumlah <= 0) {
            keranjang.splice(index, 1);
        } else {
            keranjang[index].subtotal = keranjang[index].jumlah * keranjang[index].harga;
        }
        renderCart();
    }

    function clearCart() {
        if (keranjang.length === 0) return;
        if (!confirm('Kosongkan semua item di keranjang?')) return;
        keranjang = [];
        renderCart();
    }

    function renderCart() {
        grandTotal = 0;
        let totalItems = 0;

        if (keranjang.length === 0) {
            $('#cart-items').html(`
                <div id="cart-empty" class="text-center text-muted py-4">
                    <i class="mdi mdi-cart-off" style="font-size: 40px; display: block;"></i>
                    <small class="mt-2 d-block">Belum ada item dipilih</small>
                </div>`);
        } else {
            let html = `
                <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Menu</th>
                            <th class="text-center">Qty</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>`;

            keranjang.forEach((item, i) => {
                grandTotal += item.subtotal;
                totalItems += item.jumlah;
                html += `
                    <tr>
                        <td>
                            <div class="font-weight-bold" style="font-size:13px">
                                ${escHtml(item.nama)}
                            </div>
                            ${item.catatan
                                ? `<small class="text-info font-italic">"${escHtml(item.catatan)}"</small>`
                                : ''}
                            <div class="text-muted" style="font-size:11px">
                                Rp ${item.harga.toLocaleString('id-ID')}
                            </div>
                        </td>
                        <td class="text-center align-middle">
                            <div class="d-flex align-items-center justify-content-center">
                                <button class="btn btn-sm btn-outline-danger qty-btn"
                                        onclick="ubahJumlah(${i}, -1)">−</button>
                                <span class="font-weight-bold px-2">${item.jumlah}</span>
                                <button class="btn btn-sm btn-outline-primary qty-btn"
                                        onclick="ubahJumlah(${i}, 1)">+</button>
                            </div>
                        </td>
                        <td class="text-right align-middle font-weight-bold" style="font-size:13px">
                            Rp ${item.subtotal.toLocaleString('id-ID')}
                        </td>
                    </tr>`;
            });

            html += `</tbody></table></div>`;
            $('#cart-items').html(html);
        }

        $('#total-amount').text('Rp ' + grandTotal.toLocaleString('id-ID'));
        $('#total-items-text').text(totalItems + ' item');
        $('#btn-checkout').prop('disabled', keranjang.length === 0);
    }

    function escHtml(str) {
        return $('<div>').text(str).html();
    }

    /* ─── Filter / Search ─── */
    function filterMenu() {
        const q = $('#search-input').val().toLowerCase().trim();
        $('.menu-item-wrap').each(function () {
            $(this).toggle($(this).data('search').includes(q));
        });
        // Sembunyikan vendor section jika tidak ada menu yang match
        $('.vendor-block').each(function () {
            const ada = $(this).find('.menu-item-wrap:visible').length > 0;
            $(this).toggle(q === '' || ada);
        });
    }

    /* ─── Checkout / Midtrans ─── */
    function prosesCheckout() {
        const btn = $('#btn-checkout');
        btn.prop('disabled', true)
           .html('<i class="mdi mdi-loading mdi-spin"></i> Memproses...');

        $.ajax({
            url: "{{ route('kantin.checkout') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                total_bayar: grandTotal,
                cart: keranjang
            },
            success: function (res) {
                window.snap.pay(res.snap_token, {
                    onSuccess: function () {
                        window.location.href = "{{ url('/kantin/selesai') }}";
                    },
                    onPending: function () {
                        window.location.href = "{{ url('/kantin/pending') }}";
                    },
                    onError: function () {
                        alert('Pembayaran gagal. Silakan coba lagi.');
                        resetBtn();
                    },
                    onClose: function () {
                        resetBtn();
                    }
                });
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message ?? 'Gagal menghubungi server.';
                alert(msg);
                resetBtn();
            }
        });
    }

    function resetBtn() {
        $('#btn-checkout')
            .prop('disabled', false)
            .text('BAYAR SEKARANG');
    }
</script>
@endsection