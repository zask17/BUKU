<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use Midtrans\Config;
use Midtrans\Notification;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    // /**
    //  * Handle Midtrans Payment Notification (Webhook)
    //  */
    // public function callback(Request $request)
    // {
    //     // 1. Konfigurasi Midtrans
    //     Config::$serverKey = config('midtrans.server_key');
    //     Config::$isProduction = config('midtrans.is_production');
    //     Config::$isSanitized = true;
    //     Config::$is3ds = true;

    //     try {
    //         // 2. Inisialisasi Notifikasi dari Midtrans
    //         $notification = new Notification();
            
    //         $transaction = $notification->transaction_status;
    //         $type = $notification->payment_type;
    //         $orderId = $notification->order_id; // Ini adalah order_id_pg di database kita
    //         $fraud = $notification->fraud_status;

    //         // Debugging: Catat log jika diperlukan (cek di storage/logs/laravel.log)
    //         Log::info("Midtrans Callback Received: Order ID $orderId - Status: $transaction");

    //         // 3. Cari data pesanan berdasarkan order_id_pg
    //         $pesanan = Pesanan::where('order_id_pg', $orderId)->first();

    //         if (!$pesanan) {
    //             Log::warning("Callback Error: Order ID $orderId tidak ditemukan di database.");
    //             return response()->json(['message' => 'Order not found'], 404);
    //         }

    //         // 4. Logika Perubahan Status Berdasarkan Respon Midtrans
    //         if ($transaction == 'capture') {
    //             if ($type == 'credit_card') {
    //                 if ($fraud == 'challenge') {
    //                     $pesanan->update(['status_bayar' => 0]); // Masih Pending (Challenge)
    //                 } else {
    //                     $pesanan->update(['status_bayar' => 1]); // Lunas
    //                 }
    //             }
    //         } elseif ($transaction == 'settlement') {
    //             // Status 'settlement' umum digunakan untuk QRIS, Gopay, dan Transfer Bank
    //             $pesanan->update(['status_bayar' => 1]); // Lunas
    //         } elseif ($transaction == 'pending') {
    //             $pesanan->update(['status_bayar' => 0]); // Tetap Pending
    //         } elseif ($transaction == 'deny' || $transaction == 'expire' || $transaction == 'cancel') {
    //             // Status gagal atau kadaluarsa
    //             $pesanan->update(['status_bayar' => 2]); // Gagal
    //         }

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Notification processed successfully'
    //         ], 200);

    //     } catch (\Exception $e) {
    //         Log::error("Midtrans Callback Exception: " . $e->getMessage());
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function callback(Request $request)
    {
        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        try {
            $notification = new Notification();

            $transaction = $notification->transaction_status;
            $type = $notification->payment_type;
            $orderId = $notification->order_id; // Ini adalah order_id_pg kita
            $fraud = $notification->fraud_status;

            // Cari data pesanan berdasarkan order_id_pg
            $pesanan = Pesanan::where('order_id_pg', $orderId)->first();

            if (!$pesanan) {
                return response()->json(['message' => 'Order tidak ditemukan'], 404);
            }

            // Logika Perubahan Status
            if ($transaction == 'capture') {
                if ($type == 'credit_card') {
                    if ($fraud == 'challenge') {
                        $pesanan->update(['status_bayar' => 0]); // Pending
                    } else {
                        $pesanan->update(['status_bayar' => 1]); // Lunas
                    }
                }
            } elseif ($transaction == 'settlement') {
                $pesanan->update(['status_bayar' => 1]); // Lunas
            } elseif ($transaction == 'pending') {
                $pesanan->update(['status_bayar' => 0]); // Pending
            } elseif ($transaction == 'deny' || $transaction == 'expire' || $transaction == 'cancel') {
                $pesanan->update(['status_bayar' => 2]); // Gagal
            }

            return response()->json(['message' => 'Callback diproses']);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}