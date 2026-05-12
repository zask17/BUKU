<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Toko; // Gunakan model Toko
use App\Models\Sales; // Gunakan model Sales
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    public function index()
    {
        // Ganti 'lokasi_toko' menjadi model Toko
        $listToko = Toko::all(); 
        return view('sales.dashboard-sales', compact('listToko'));
    }

    /**
     * Lookup toko berdasarkan barcode (idtoko)
     * Digunakan oleh AJAX saat sales scan barcode
     */
    public function findByBarcode($id)
    {
        $toko = Toko::where('idtoko', $id)->first();

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak ditemukan atau barcode tidak valid.'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Toko ditemukan',
            'data' => $toko
        ]);
    }

    public function storeVisit(Request $request)
    {
        $request->validate([
            'barcode' => 'required',
            'sales_lat' => 'required',
            'sales_long' => 'required',
            'sales_acc' => 'required',
        ]);

        // Cari toko berdasarkan idtoko (barcode di sini diasumsikan sebagai idtoko atau primary key)
        $toko = Toko::where('idtoko', $request->barcode)->first();

        if (!$toko) {
            return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan!']);
        }

        // Hitung jarak (Formula Haversine)
        $jarakAktual = $this->haversine(
            $request->sales_lat, $request->sales_long,
            $toko->latitude, $toko->longtitude // Perhatikan typo 'longtitude' di model Toko Anda
        );

        // Threshold: Radius 300m + Akurasi Toko + Akurasi Sales [cite: 89]
        $radiusMax = 300; 
        $thresholdEfektif = $radiusMax + $toko->accuracy + $request->sales_acc;

        $isAccepted = $jarakAktual <= $thresholdEfektif;

        // Simpan ke tabel sales menggunakan model Sales
        Sales::create([
            'idtoko'    => $toko->idtoko,
            'latitude'  => $request->sales_lat,
            'longitude' => $request->sales_long,
            'accuracy'  => $request->sales_acc,
            'jarak'     => $jarakAktual,
            'status'    => $isAccepted ? 'DITERIMA' : 'DITOLAK',
            'waktu'     => now(),
        ]);

        if ($isAccepted) {
            return response()->json([
                'status' => 'success',
                'message' => 'Kunjungan DITERIMA. Jarak: ' . round($jarakAktual, 2) . 'm'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Kunjungan DITOLAK. Jarak: ' . round($jarakAktual, 2) . 'm (Maks: '.round($thresholdEfektif).'m)'
            ]);
        }
    }

    private function haversine($lat1, $lng1, $lat2, $lng2)
    {
        $R = 6371000; 
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat/2)**2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2)**2;
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R * $c;
    }
}