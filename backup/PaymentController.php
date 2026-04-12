<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function callback(Request $request)
    {
        // Mengambil server key dari config/midtrans.php 
        $serverKey = config('midtrans.server_key');
        
        // Verifikasi Signature untuk keamanan transaksi agar tidak dimanipulasi
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            $pesanan = Pesanan::where('order_id_pg', $request->order_id)->first();
            
            if (!$pesanan) return response()->json(['message' => 'Pesanan tidak ditemukan'], 404);

            // Jika pembayaran berhasil (Lunas), ubah status menjadi 1
            if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                $pesanan->update([
                    'status_bayar' => 1, // 1 = Lunas
                    'metode_bayar' => $request->payment_type // Menyimpan metode bayar (VA/QRIS)
                ]);
            } 
            // Jika expired, cancel, atau deny, ubah status menjadi 2 (Gagal)
            elseif (in_array($request->transaction_status, ['expire', 'cancel', 'deny'])) {
                $pesanan->update(['status_bayar' => 2]); // 2 = Gagal/Expired   
            }
        }

        return response()->json(['status' => 'OK']);
    }
}