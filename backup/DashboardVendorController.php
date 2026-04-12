<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DetailPesanan;

class DashboardVendorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.menu.index')->with('error', 'Anda belum terdaftar sebagai vendor');
        }

        // Get semua menu dari vendor ini
        $menuIds = $vendor->menus()->pluck('idmenu')->toArray();

        // Get detail pesanan yang lunas (status_bayar = 1) dari menu vendor
        $pesananLunas = DetailPesanan::whereIn('idmenu', $menuIds)
            ->with(['pesanan', 'menu'])
            ->whereHas('pesanan', function($query) {
                $query->where('status_bayar', 1); // Lunas
            })
            ->orderBy('timestamp', 'desc')
            ->get()
            ->groupBy('idpesanan');

        // Statistik untuk dashboard vendor
        $stats = [
            'total_menu'       => count($menuIds),
            'total_pesanan_lunas' => $pesananLunas->count(),
            'total_pendapatan' => DetailPesanan::whereIn('idmenu', $menuIds)
                ->whereHas('pesanan', function($query) {
                    $query->where('status_bayar', 1);
                })
                ->sum('subtotal'),
            'pesanan_hari_ini' => DetailPesanan::whereIn('idmenu', $menuIds)
                ->whereHas('pesanan', function($query) {
                    $query->where('status_bayar', 1)
                          ->whereDate('timestamp', today());
                })
                ->count(),
        ];

        return view('vendor.dashboard-vendor', compact('vendor', 'pesananLunas', 'stats'));
    }
}