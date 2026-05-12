<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sales;
use App\Models\Toko;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama sales
     */
    public function dashboard()
    {
        // Mengambil data riwayat kunjungan terakhir dari tabel sales beserta relasi tokonya
        $riwayat = Sales::with('toko')
                        ->orderBy('waktu', 'desc')
                        ->take(10)
                        ->get();

        // Mengembalikan view khusus dashboard-sales [sesuai permintaan user]
        return view('sales.dashboard-sales', compact('riwayat'));
    }

    /**
     * Mencari data toko berdasarkan barcode (idtoko)
     * Digunakan oleh AJAX pada sales.js saat proses scan
     */
    public function findByBarcode($id)
    {
        $toko = Toko::where('idtoko', $id)->first();

        if ($toko) {
            return response()->json([
                'success' => true,
                'data' => $toko
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Toko tidak ditemukan atau barcode tidak valid.'
        ]);
    }

    /**
     * Menyimpan data kunjungan sales setelah divalidasi geolokasinya
     */
    public function store(Request $request)
    {
        // Validasi input dari request AJAX 
        $request->validate([
            'idtoko'    => 'required|integer',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy'  => 'required|numeric',
            'jarak'     => 'required|numeric',
            'status'    => 'required|string|in:diterima,ditolak',
        ]);

        try {
            // Simpan ke tabel sales 
            $sales = Sales::create([
                'idtoko'    => $request->idtoko,
                'latitude'  => $request->latitude,
                'longitude' => $request->longitude,
                'accuracy'  => $request->accuracy,
                'jarak'     => $request->jarak,
                'status'    => $request->status,
                'waktu'     => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kunjungan berhasil dicatat.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }
}