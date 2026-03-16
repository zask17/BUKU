<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    private function getLayout() {
        return (Auth::user()->idrole == 1) ? 'layouts.admin.main' : 'layouts.visitor.main';
    }

    public function indexAxios() {
        $layout = $this->getLayout();
        $barangs = Barang::all(); 
        
        // Panggil fungsi getRiwayat() agar data detail (kasir, nama_barang, dll) muncul
        $riwayat = $this->getRiwayat();

        return view('admin.pos.index_axios', compact('barangs', 'layout', 'riwayat'));
    }
    public function indexAjax() {
        $layout = $this->getLayout();
        $barangs = Barang::all();
        
        // Panggil fungsi getRiwayat() 
        $riwayat = $this->getRiwayat();

        return view('admin.pos.index_ajax', compact('barangs', 'layout', 'riwayat'));
    }

    private function getRiwayat() {
        return DB::table('penjualan_detail')
            ->join('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id_penjualan')
            ->join('users', 'penjualan.iduser', '=', 'users.iduser')
            ->join('barang', 'penjualan_detail.id_barang', '=', 'barang.id_barang')
            ->select(
                'penjualan.id_penjualan',
                'penjualan.timestamp',
                'penjualan.total as total_transaksi',
                'users.nama_user as kasir',
                'barang.nama as nama_barang',
                'penjualan_detail.jumlah',
                'penjualan_detail.subtotal'
            )
            ->orderBy('penjualan.timestamp', 'desc')
            ->orderBy('penjualan.id_penjualan', 'desc')
            ->get();
    }

    public function cekBarang(Request $request) {
        $barang = Barang::where('id_barang', $request->kode)->first();
        if ($barang) {
            return response()->json(['status' => 'success', 'data' => $barang]);
        }
        return response()->json(['status' => 'error', 'msg' => 'Barang tidak ditemukan'], 404);
    }

    public function store(Request $request) {
        DB::beginTransaction();
        try {
            $id_penjualan = DB::table('penjualan')->insertGetId([
                'timestamp' => now(),
                'total'     => $request->total_harga,
                'iduser'    => Auth::user()->iduser,
            ], 'id_penjualan');

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