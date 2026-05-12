<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Toko;
use App\Models\Sales;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function dashboard()
    {
        $riwayat = Sales::with('toko')->orderBy('waktu', 'desc')->take(10)->get();
        return view('sales.dashboard', compact('riwayat'));
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
            'message' => 'Toko tidak terdaftar di sistem.'
        ]);
    }

    public function store(Request $request)
    {
        try {
            $sales = Sales::create([
                'idtoko' => $request->idtoko,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'accuracy' => $request->accuracy,
                'jarak' => $request->jarak,
                'status' => $request->status,
                'waktu' => now()
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}