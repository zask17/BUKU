<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    public function indexAxios() {
        $layout = (Auth::user()->idrole == 1) ? 'layouts.admin.main' : 'layouts.visitor.main';
        $barangs = Barang::all();
        
        // Ambil riwayat transaksi terbaru
        $riwayat = DB::table('penjualan')
                    ->join('users', 'penjualan.iduser', '=', 'users.iduser')
                    ->select('penjualan.*', 'users.nama_user')
                    ->orderBy('penjualan.timestamp', 'desc')
                    ->get();

        return view('admin.pos.index_axios', compact('barangs', 'layout', 'riwayat'));
    }

    public function indexAjax() {
        $layout = (Auth::user()->idrole == 1) ? 'layouts.admin.main' : 'layouts.visitor.main';
        $barangs = Barang::all();
        
        $riwayat = DB::table('penjualan')
                    ->join('users', 'penjualan.iduser', '=', 'users.iduser')
                    ->select('penjualan.*', 'users.nama_user')
                    ->orderBy('penjualan.timestamp', 'desc')
                    ->get();

        return view('admin.pos.index_ajax', compact('barangs', 'layout', 'riwayat'));
    }

    public function store(Request $request) {
        DB::beginTransaction();
        try {
            // 1. Simpan ke tabel penjualan
            $id_penjualan = DB::table('penjualan')->insertGetId([
                'timestamp' => now(),
                'total'     => $request->total_harga,
                'iduser'    => Auth::user()->iduser,
            ], 'id_penjualan');

            // 2. Simpan detail barang
            foreach ($request->items as $item) {
                DB::table('penjualan_detail')->insert([
                    'id_penjualan' => $id_penjualan,
                    'id_barang'    => $item['id_barang'],
                    'jumlah'       => $item['qty'],
                    'subtotal'     => $item['subtotal'],
                ]);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'msg' => 'Transaksi Berhasil!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'msg' => $e->getMessage()], 500);
        }
    }
}