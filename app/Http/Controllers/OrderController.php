<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        // Menampilkan halaman pemesanan (POS) untuk Customer 
        $menus = Menu::all();
        return view('customer.order', compact('menus'));
    }

    public function checkout(Request $request)
    {
        // 1. Logika User Guest Otomatis (Guest_0000001) [cite: 4]
        $latestOrder = Pesanan::orderBy('idpesanan', 'desc')->first();
        $nextId = $latestOrder ? $latestOrder->idpesanan + 1 : 1;
        
        // str_pad diset 7 digit agar menghasilkan 0000001 [cite: 4]
        $guestName = "Guest_" . str_pad($nextId, 7, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
            // 2. Simpan Header Pesanan [cite: 11]
            $pesanan = Pesanan::create([
                'nama'         => $guestName,
                'total'        => $request->total_bayar,
                'status_bayar' => 0, // 0 = Pending [cite: 11]
                'order_id_pg'  => 'KANTIN-' . time() . '-' . $nextId // ID Unik untuk Midtrans [cite: 11]
            ]);

            // 3. Simpan Detail Pesanan (Looping dari Cart) [cite: 11]
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

            // 4. Konfigurasi Midtrans menggunakan config/midtrans.php [cite: 1, 13]
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
            \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

            $params = [
                'transaction_details' => [
                    'order_id'     => $pesanan->order_id_pg,
                    'gross_amount' => (int)$pesanan->total,
                ],
                'customer_details' => [
                    'first_name' => $guestName,
                ],
                // Mengaktifkan VA dan QRIS sesuai instruksi Modul 6 [cite: 6]
                'enabled_payments' => ['credit_card', 'gopay', 'shopeepay', 'permata_va', 'bca_va', 'bni_va', 'bri_va', 'other_va'],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);
            
            // Simpan token ke database untuk keperluan re-payment jika perlu [cite: 11]
            $pesanan->update(['snap_token' => $snapToken]);

            return response()->json(['snap_token' => $snapToken]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}