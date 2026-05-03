<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Vendor;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use Midtrans\Config;
use Midtrans\Snap;

class KantinController extends Controller
{
    private function getLayout()
    {
        if (Auth::check()) {
            $role = Auth::user()->idrole;
            if ($role == 1) {
                return 'layouts.admin.main';
            } elseif ($role == 3) {
                return 'layouts.vendor.main';
            }
            return 'layouts.visitor.main';
        }
        return 'layouts.guest.main';
    }

    public function index()
    {
        $layout = $this->getLayout();
        $vendors = Vendor::with('menus')->get();
        $pesanan = Pesanan::orderBy('timestamp', 'desc')->get();

        return view('kantin.index', compact('vendors', 'pesanan', 'layout'));
    }

    public function selesai($id)
    {
        $layout = $this->getLayout();

        $pesanan = Pesanan::with([
            'details.menu.vendor'
        ])->findOrFail($id);

        if ($pesanan->status_bayar != 1) {
            return redirect()->route('kantin.pending')
                ->with('error', 'Pembayaran belum selesai.');
        }

        $qrcode = QrCode::size(300)
            ->format('svg')
            ->generate($pesanan->idpesanan);

        return view('kantin.selesai', compact('pesanan', 'qrcode', 'layout'));
    }

    public function pending()
    {
        $layout = $this->getLayout();
        return view('kantin.pending', compact('layout'));
    }

    public function gagal()
    {
        $layout = $this->getLayout();
        return view('kantin.gagal', compact('layout'));
    }

    /**
     * API: Mendapatkan detail pesanan khusus untuk vendor yang sedang login
     */

    public function getOrderDetails($idpesanan)
    {
        try {
            // Load pesanan dengan relasi menu dan vendor
            $pesanan = Pesanan::with(['details.menu.vendor'])->findOrFail($idpesanan);
            
            // Ambil profil vendor dari user yang sedang login
            $vendor = Auth::user()->vendor;

            if (!$vendor) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Akun Anda tidak terdaftar sebagai vendor resmi'
                ], 403);
            }

            // FILTER: Hanya ambil detail menu yang dimiliki oleh vendor yang sedang scan
            $filteredDetails = $pesanan->details->filter(function ($detail) use ($vendor) {
                return $detail->menu && $detail->menu->idvendor === $vendor->idvendor;
            });

            // Jika pesanan ada tapi tidak ada menu milik vendor ini
            if ($filteredDetails->isEmpty()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Tidak ada pesanan untuk vendor ini' . $vendor->nama_vendor
                ], 403);
            }

            // Hitung subtotal khusus untuk porsi vendor ini
            $subtotalVendor = $filteredDetails->sum('subtotal');

            return response()->json([
                'success' => true,
                'data' => [
                    'idpesanan'         => $pesanan->idpesanan,
                    'nama_customer'     => $pesanan->nama,
                    'nama_vendor'       => $vendor->nama_vendor,
                    'total_transaksi'   => number_format($pesanan->total, 0, ',', '.'),
                    'subtotal_vendor'   => number_format($subtotalVendor, 0, ',', '.'),
                    'status_bayar_text' => $pesanan->status_bayar == 1 ? 'Lunas / Paid' : 'Pending',
                    'items' => $filteredDetails->map(function ($detail) {
                        return [
                            'nama_menu' => $detail->menu->nama_menu,
                            'jumlah'    => $detail->jumlah,
                            'harga'     => number_format($detail->harga, 0, ',', '.'),
                            'subtotal'  => number_format($detail->subtotal, 0, ',', '.'),
                            'catatan'   => $detail->catatan ?? '-'
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Data pesanan tidak ditemukan di sistem'
            ], 404);
        }
    }

    // public function getOrderDetails($idpesanan)
    // {
    //     try {
    //         // Load pesanan beserta relasi menu dan vendor
    //         $pesanan = Pesanan::with(['details.menu.vendor'])->findOrFail($idpesanan);
    //         $vendor = Auth::user()->vendor;

    //         if (!$vendor) {
    //             return response()->json(['success' => false, 'message' => 'Anda tidak terdaftar sebagai vendor'], 403);
    //         }

    //         // Filter menu hanya untuk vendor yang sedang login
    //         $filteredDetails = $pesanan->details->filter(function ($detail) use ($vendor) {
    //             return $detail->menu && $detail->menu->idvendor === $vendor->idvendor;
    //         });

    //         if ($filteredDetails->isEmpty()) {
    //             return response()->json(['success' => false, 'message' => 'Pesanan ini tidak berisi menu dari vendor Anda'], 403);
    //         }

    //         // Hitung subtotal khusus untuk vendor ini
    //         $subtotalVendor = $filteredDetails->sum('subtotal');

    //         return response()->json([
    //             'success' => true,
    //             'data' => [
    //                 'idpesanan'         => $pesanan->idpesanan,
    //                 'nama'              => $pesanan->nama,
    //                 'nama_vendor'       => $vendor->nama_vendor,
    //                 'total_seluruhnya'  => number_format($pesanan->total, 0, ',', '.'),
    //                 'total_vendor'      => number_format($subtotalVendor, 0, ',', '.'), // Subtotal khusus vendor ini
    //                 'status_bayar_text' => $pesanan->status_bayar == 1 ? 'Lunas / Paid' : 'Pending',
    //                 'items' => $filteredDetails->map(function ($detail) {
    //                     return [
    //                         'nama_menu' => $detail->menu->nama_menu,
    //                         'jumlah'    => $detail->jumlah,
    //                         'harga'     => number_format($detail->harga, 0, ',', '.'),
    //                         'subtotal'  => number_format($detail->subtotal, 0, ',', '.'),
    //                         'catatan'   => $detail->catatan ?? '-'
    //                     ];
    //                 })
    //             ]
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json(['success' => false, 'message' => 'Data pesanan tidak ditemukan'], 404);
    //     }
    // }

    public function checkout(Request $request)
    {
        $request->validate(['total_bayar' => 'required|integer|min:1', 'cart' => 'required|array|min:1']);
        try {
            DB::beginTransaction();
            // 1. Ambil urutan terakhir dari tabel pesanan
            $lastOrder = Pesanan::orderBy('idpesanan', 'desc')->first();
            $nextNumber = $lastOrder ? $lastOrder->idpesanan + 1 : 1;

            // 2. Format pesanan =  KANTIN-0000001
            $orderId = 'KANTIN-' . str_pad($nextNumber, 12, '0', STR_PAD_LEFT);

            // 3. Format pemesan menjadi Guest_0000001 jika tidak login, atau nama_user jika login
            if (Auth::check()) {
                $nama = Auth::user()->nama_user;
            } else {
                $nama = 'Guest_' . str_pad($nextNumber, 12, '0', STR_PAD_LEFT);
            }

            $pesanan = Pesanan::create([
                'nama' => $nama,
                'timestamp' => now(),
                'total' => $request->total_bayar,
                'status_bayar' => 0,
                'order_id_pg' => $orderId,
            ]);

            foreach ($request->cart as $item) {
                DetailPesanan::create([
                    'idpesanan' => $pesanan->idpesanan,
                    'idmenu' => $item['idmenu'],
                    'jumlah' => $item['jumlah'],
                    'harga' => $item['harga'],
                    'subtotal' => $item['subtotal'],
                    'catatan' => $item['catatan'] ?? null,
                    'timestamp' => now(),
                ]);
            }
            DB::commit();

            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $params = [
                'transaction_details' => ['order_id' => $orderId, 'gross_amount' => (int) $request->total_bayar],
                'customer_details' => ['first_name' => $nama],
                'item_details' => collect($request->cart)->map(fn($i) => [
                    'id' => $i['idmenu'],
                    'price' => (int)$i['harga'],
                    'quantity' => (int)$i['jumlah'],
                    'name' => substr($i['nama'], 0, 50)
                ])->toArray(),
            ];

            $snapToken = Snap::getSnapToken($params);
            $pesanan->update(['snap_token' => $snapToken]);
            session(['order_id' => $orderId]);

            return response()->json([
                'snap_token' => $snapToken,
                'idpesanan'  => $pesanan->idpesanan
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
