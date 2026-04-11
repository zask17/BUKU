<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardVendorController extends Controller
{
    public function index()
    {
        // Mengambil data pesanan yang status_bayar-nya 1 (Lunas)
        $pesananLunas = DB::table('pesanan')
            ->where('status_bayar', 1)
            ->orderBy('timestamp', 'desc')
            ->get();

        // Statistik sederhana untuk dashboard vendor
        $stats = [
            'total_pendapatan' => DB::table('pesanan')->where('status_bayar', 1)->sum('total'),
            'jumlah_pesanan'   => $pesananLunas->count(),
            'total_menu'       => DB::table('menu')->count(),
        ];

        return view('vendor.dashboard-vendor', compact('pesananLunas', 'stats'));
    }
}