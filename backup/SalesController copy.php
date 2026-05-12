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
    public function dashboard()
    {
        $riwayat = Sales::with('toko')
                        ->orderBy('waktu', 'desc')
                        ->take(10)
                        ->get();
        return view('sales.dashboard-sales', compact('riwayat'));
    }

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

    public function store(Request $request)
    {
        $request->validate([
            'idtoko'    => 'required|integer',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy'  => 'required|numeric',
            'jarak'     => 'required|numeric',
            'status'    => 'required|string|in:diterima,ditolak',
        ]);

        try {
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