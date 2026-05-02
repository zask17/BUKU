<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\DetailPesanan;
use App\Models\Vendor;
use Carbon\Carbon;

class DashboardVendorController extends Controller
{
    private function getVendor()
    {
        return Auth::user()->vendor;
    }

    /**
     * Menampilkan Dashboard Utama (Statistik & Ringkasan Lunas)
     */
    public function index()
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return redirect()->back()->with('error', 'Akun Anda tidak terdaftar sebagai Vendor.');
        }

        $menuIds = $vendor->menus()->pluck('idmenu')->toArray();

        $pesananLunas = DetailPesanan::whereIn('idmenu', $menuIds)
            ->with(['pesanan', 'menu'])
            ->whereHas('pesanan', function ($query) {
                $query->where('status_bayar', 1);
            })
            ->orderBy('timestamp', 'desc')
            ->get()
            ->groupBy('idpesanan');

        $stats = [
            'total_menu'       => count($menuIds),
            'total_pendapatan' => DetailPesanan::whereIn('idmenu', $menuIds)
                ->whereHas('pesanan', fn($q) => $q->where('status_bayar', 1))
                ->sum('subtotal'),
            'pesanan_hari_ini' => DetailPesanan::whereIn('idmenu', $menuIds)
                ->whereHas('pesanan', fn($q) => $q->where('status_bayar', 1)->whereDate('timestamp', Carbon::today()))
                ->count(),
        ];

        return view('vendor.dashboard-vendor', compact('pesananLunas', 'stats'));
    }

    /**
     * Menampilkan Halaman Manajemen Pesanan
     */
    public function pesanan()
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return redirect()->route('welcome')->with('error', 'Data Vendor tidak ditemukan.');
        }

        $menuIds = $vendor->menus()->pluck('idmenu')->toArray();

        $orders = DetailPesanan::whereIn('idmenu', $menuIds)
            ->with(['pesanan', 'menu'])
            ->orderBy('timestamp', 'desc')
            ->get();

        return view('vendor.pesanan.index', compact('orders'));
    }

    public function scannerQRCode()
    {
        $vendor = $this->getVendor();

        if (!$vendor) {
            return redirect()->back()->with('error', 'Data Vendor tidak ditemukan.');
        }

        return view('vendor.pesanan.scanner-qrcode', compact('vendor'));
    }
}