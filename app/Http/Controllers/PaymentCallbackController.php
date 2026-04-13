<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use Midtrans\Config;
use Midtrans\Notification;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    public function callback(Request $request)
    {
        // 1. Log setiap kali ada data masuk dari Midtrans
        Log::info('Midtrans Callback Received: ', $request->all());

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        try {
            $notification = new Notification();

            $transaction = $notification->transaction_status;
            $type = $notification->payment_type;
            $orderId = $notification->order_id;
            $fraud = $notification->fraud_status;

            // 2. Log data penting yang sudah diekstrak
            Log::info("Processing Order ID: $orderId | Status: $transaction | Type: $type");

            $pesanan = Pesanan::where('order_id_pg', $orderId)->first();

            if (!$pesanan) {
                Log::error("Order ID $orderId tidak ditemukan di database.");
                return response()->json(['message' => 'Order tidak ditemukan'], 404);
            }

            // Logika Perubahan Status
            if ($transaction == 'capture') {
                if ($type == 'credit_card') {
                    if ($fraud == 'challenge') {
                        $pesanan->update(['status_bayar' => 0]);
                    } else {
                        $pesanan->update(['status_bayar' => 1]);
                    }
                }
            } elseif ($transaction == 'settlement') {
                $pesanan->update(['status_bayar' => 1]);
                Log::info("Order ID $orderId Berhasil Diupdate ke LUNAS");
            } elseif ($transaction == 'pending') {
                $pesanan->update(['status_bayar' => 0]);
            } elseif ($transaction == 'deny' || $transaction == 'expire' || $transaction == 'cancel') {
                $pesanan->update(['status_bayar' => 2]);
                Log::warning("Order ID $orderId GAGAL/EXPIRED");
            }

            return response()->json(['message' => 'Callback diproses']);

        } catch (\Exception $e) {
            // 3. Log kalau ada error coding atau koneksi
            Log::error('Error Callback Midtrans: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
// GATAU DEH INI BISA TAPI MASIH TETEP PENDING <?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\Pesanan;
// use Midtrans\Config;
// use Midtrans\Notification;
// use Illuminate\Support\Facades\Log;

// class PaymentCallbackController extends Controller
// {
//     public function callback(Request $request)
//     {
//         // 1. Konfigurasi Midtrans
//         Config::$serverKey = config('midtrans.server_key');
//         Config::$isProduction = config('midtrans.is_production');
//         Config::$isSanitized = true;
//         Config::$is3ds = true;

//         try {
//             $notification = new Notification();

//             $status = $notification->transaction_status;
//             $type = $notification->payment_type;
//             $orderId = $notification->order_id; // Ini order_id_pg di database
//             $fraud = $notification->fraud_status;

//             // 2. Cari data pesanan berdasarkan order_id_pg
//             $pesanan = Pesanan::where('order_id_pg', $notification->order_id)->first();

//             if (!$pesanan) {
//                 return response()->json(['message' => 'Order ID tidak ditemukan'], 404);
//             }

//             // 3. Logika Perubahan Status Berdasarkan Respon Midtrans
//             // 0 = Pending, 1 = Lunas, 2 = Gagal/Expired
//             if ($status == 'capture') {
//                 if ($type == 'credit_card') {
//                     if ($fraud == 'challenge') {
//                         $pesanan->update(['status_bayar' => 0]);
//                     } else {
//                         $pesanan->update(['status_bayar' => 1]);
//                     }
//                 }
//             } elseif ($status == 'settlement') {
//                 // Status ini untuk QRIS, GoPay, dan Transfer Bank (Lunas)
//                 $pesanan->update(['status_bayar' => 1]);
//             } elseif ($status == 'pending') {
//                 $pesanan->update(['status_bayar' => 0]);
//             } elseif ($status == 'deny' || $status == 'expire' || $status == 'cancel') {
//                 $pesanan->update(['status_bayar' => 2]);
//             }

//             return response()->json(['message' => 'Callback Midtrans berhasil diproses'], 200);
//         } catch (\Exception $e) {
//             Log::error("Midtrans Callback Error: " . $e->getMessage());
//             return response()->json(['message' => $e->getMessage()], 500);
//         }
//     }
// }

//INI SUDAH BENER <?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\Pesanan;
// use Midtrans\Config;
// use Midtrans\Notification;
// use Illuminate\Support\Facades\Log;

// class PaymentCallbackController extends Controller
// {
//     public function callback(Request $request)
//     {
//         // Konfigurasi Midtrans
//         Config::$serverKey = config('midtrans.server_key');
//         Config::$isProduction = config('midtrans.is_production');
//         Config::$isSanitized = true;
//         Config::$is3ds = true;

//         try {
//             $notification = new Notification();

//             $transaction = $notification->transaction_status;
//             $type = $notification->payment_type;
//             $orderId = $notification->order_id; // Ini adalah order_id_pg kita
//             $fraud = $notification->fraud_status;

//             // Cari data pesanan berdasarkan order_id_pg
//             $pesanan = Pesanan::where('order_id_pg', $orderId)->first();

//             if (!$pesanan) {
//                 return response()->json(['message' => 'Order tidak ditemukan'], 404);
//             }

//             // Logika Perubahan Status
//             if ($transaction == 'capture') {
//                 if ($type == 'credit_card') {
//                     if ($fraud == 'challenge') {
//                         $pesanan->update(['status_bayar' => 0]); // Pending
//                     } else {
//                         $pesanan->update(['status_bayar' => 1]); // Lunas
//                     }
//                 }
//             } elseif ($transaction == 'settlement') {
//                 $pesanan->update(['status_bayar' => 1]); // Lunas
//             } elseif ($transaction == 'pending') {
//                 $pesanan->update(['status_bayar' => 0]); // Pending
//             } elseif ($transaction == 'deny' || $transaction == 'expire' || $transaction == 'cancel') {
//                 $pesanan->update(['status_bayar' => 2]); // Gagal
//             }

//             return response()->json(['message' => 'Callback diproses']);

//         } catch (\Exception $e) {
//             return response()->json(['message' => $e->getMessage()], 500);
//         }
//     }
// }

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