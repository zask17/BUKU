<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function callback(Request $request)
    {
        // Mengambil server key dari config/midtrans.php 
        $serverKey = config('midtrans.server_key');
        
        // Verifikasi Signature untuk keamanan transaksi agar tidak dimanipulasi [cite: 11]
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            $pesanan = Pesanan::where('order_id_pg', $request->order_id)->first();
            
            if (!$pesanan) return response()->json(['message' => 'Pesanan tidak ditemukan'], 404);

            // Jika pembayaran berhasil (Lunas), ubah status menjadi 1 [cite: 7, 11]
            if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                $pesanan->update([
                    'status_bayar' => 1, // 1 = Lunas [cite: 7, 11]
                    'metode_bayar' => $request->payment_type // Menyimpan metode bayar (VA/QRIS) [cite: 6, 11]
                ]);
            } 
            // Jika expired, cancel, atau deny, ubah status menjadi 2 (Gagal) [cite: 11]
            elseif (in_array($request->transaction_status, ['expire', 'cancel', 'deny'])) {
                $pesanan->update(['status_bayar' => 2]); // 2 = Gagal/Expired [cite: 11]
            }
        }

        return response()->json(['status' => 'OK']);
    }
}