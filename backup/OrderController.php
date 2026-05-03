<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Menu;
use App\Models\Vendor; // Tambahkan model Vendor
use Illuminate\Http\Request;
use \Midtrans\Snap;
use \Midtrans\Config;

class OrderController extends Controller
{
    public function index()
    {
        // Mengambil vendor yang memiliki menu agar bisa dipisahkan per kategori vendor 
        $vendors = Vendor::with('menus')->get();
        return view('guest.order', compact('vendors'));
    }

    public function checkout(Request $request)
    {
        // 1. Logika User Guest Otomatis (Guest_0000001)
        $latestOrder = Pesanan::orderBy('idpesanan', 'desc')->first();
        $nextId = $latestOrder ? $latestOrder->idpesanan + 1 : 1;
        $guestName = "Guest_" . str_pad($nextId, 7, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
            // 2. Simpan Header Pesanan
            $pesanan = Pesanan::create([
                'nama'         => $guestName,
                'total'        => $request->total_bayar,
                'status_bayar' => 0,
                'order_id_pg'  => 'KANTIN-' . time() . '-' . $nextId
            ]);

            // 3. Simpan Detail Pesanan
            // Simpan Detail Pesanan di OrderController
            foreach ($request->cart as $item) {
                DetailPesanan::create([
                    'idmenu'     => $item['idmenu'],
                    'idpesanan'  => $pesanan->idpesanan,
                    'jumlah'     => $item['jumlah'],
                    'harga'      => $item['harga'],
                    'subtotal'   => $item['jumlah'] * $item['harga'],
                    'catatan'    => $item['catatan'] ?? null
                ]);
            }
            DB::commit();

            // 4. Konfigurasi Midtrans
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized = config('midtrans.is_sanitized');
            Config::$is3ds = config('midtrans.is_3ds');

            $params = [
                'transaction_details' => [
                    'order_id'     => $pesanan->order_id_pg,
                    'gross_amount' => (int)$pesanan->total,
                ],
                'customer_details' => [
                    'first_name' => $guestName,
                ],
                'enabled_payments' => ['credit_card', 'gopay', 'shopeepay', 'permata_va', 'bca_va', 'bni_va', 'bri_va', 'other_va'],
            ];

            $snapToken = Snap::getSnapToken($params);
            $pesanan->update(['snap_token' => $snapToken]);

            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
