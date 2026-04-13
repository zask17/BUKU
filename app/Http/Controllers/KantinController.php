<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
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

    public function selesai() 
    { 
        $layout = $this->getLayout();
        return view('kantin.selesai', compact('layout')); 
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

    public function checkout(Request $request)    {
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
                    'id' => $i['idmenu'], 'price' => (int)$i['harga'], 'quantity' => (int)$i['jumlah'], 'name' => substr($i['nama'], 0, 50)
                ])->toArray(),
            ];

            $snapToken = Snap::getSnapToken($params);
            $pesanan->update(['snap_token' => $snapToken]);
            session(['order_id' => $orderId]);

            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}