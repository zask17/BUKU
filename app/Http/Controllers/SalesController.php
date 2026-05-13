<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Toko; 
use App\Models\Sales; 
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    public function index()
    {
        $listToko = Toko::all(); 
        return view('sales.dashboard-sales', compact('listToko'));
    }

    public function storeVisit(Request $request)
    {
        // 1. Tampung hasil validasi ke dalam variabel $validated
        $validated = $request->validate([
            'barcode'    => 'required',
            'sales_lat'  => 'required|numeric',
            'sales_long' => 'required|numeric',
            'sales_acc'  => 'required|numeric',
        ]);

        // 2. Ambil data dari array $validated (ini akan menghilangkan peringatan VS Code)
        $barcode   = $validated['barcode'];
        $salesLat  = (float) $validated['sales_lat'];
        $salesLong = (float) $validated['sales_long'];
        $salesAcc  = (float) $validated['sales_acc'];

        // Cari toko berdasarkan idtoko
        $toko = Toko::where('idtoko', $barcode)->first();

        if (!$toko) {
            return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan!'], 404);
        }

        // 3. Hitung jarak dengan Haversine [cite: 18, 76]
        $jarakAktual = $this->haversine(
            $salesLat, 
            $salesLong,
            (float) $toko->latitude, 
            (float) $toko->longtitude // Mengikuti kolom 'longtitude' di model Toko
        );

        // 4. Tentukan Threshold Efektif [cite: 89]
        $radiusMax = 300; 
        $thresholdEfektif = $radiusMax + (float) $toko->accuracy + $salesAcc;

        $isAccepted = $jarakAktual <= $thresholdEfektif;

        // 5. Simpan ke database menggunakan model Sales
        Sales::create([
            'idtoko'    => $toko->idtoko,
            'latitude'  => $salesLat,
            'longitude' => $salesLong,
            'accuracy'  => $salesAcc,
            'jarak'     => (int) round($jarakAktual), // Konversi ke Integer untuk PostgreSQL
            'status'    => $isAccepted ? 'DITERIMA' : 'DITOLAK',
            'waktu'     => now(),
        ]);

        return response()->json([
            'status'  => $isAccepted ? 'success' : 'error',
            'message' => $isAccepted 
                ? 'Kunjungan DITERIMA. Jarak: ' . round($jarakAktual, 2) . 'm'
                : 'Kunjungan DITOLAK. Jarak: ' . round($jarakAktual, 2) . 'm'
        ]);
    }
    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $R = 6371000; 
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat/2)**2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2)**2;
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return (float) ($R * $c);
    }
}