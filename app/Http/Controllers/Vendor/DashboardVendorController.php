<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\DetailPesanan;
use App\Models\Vendor;
use Carbon\Carbon;

class DashboardVendorController extends Controller
{
    /**
     * Menampilkan Dashboard Utama (Statistik & Ringkasan Lunas)
     */
    public function index()
    {
        // 1. Ambil data vendor milik user yang sedang login
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->back()->with('error', 'Akun Anda tidak terdaftar sebagai Vendor.');
        }

        // 2. Ambil semua ID menu yang dimiliki oleh vendor ini
        $menuIds = $vendor->menus()->pluck('idmenu')->toArray();

        // 3. Ambil detail pesanan yang menu-nya milik vendor ini DAN status pesanan utamanya adalah Lunas (1)
        $pesananLunas = DetailPesanan::whereIn('idmenu', $menuIds)
            ->with(['pesanan', 'menu']) 
            ->whereHas('pesanan', function($query) {
                $query->where('status_bayar', 1); 
            })
            ->orderBy('timestamp', 'desc')
            ->get()
            ->groupBy('idpesanan'); // Dikelompokkan per transaksi agar tidak double row untuk ID yang sama

        // 4. Hitung Statistik untuk Widget Dashboard
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
     * Menampilkan Halaman Manajemen Pesanan Baru (Semua Status)
     */
    public function pesanan()
    {
        // 1. Ambil data vendor milik user login
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('welcome')->with('error', 'Data Vendor tidak ditemukan.');
        }

        // 2. Ambil ID semua menu milik vendor ini
        $menuIds = $vendor->menus()->pluck('idmenu')->toArray();

        // 3. Mengambil semua pesanan (Lunas, Pending, Gagal) agar vendor bisa memantau
        $orders = DetailPesanan::whereIn('idmenu', $menuIds)
            ->with(['pesanan', 'menu'])
            ->orderBy('timestamp', 'desc')
            ->get();

        return view('vendor.pesanan.index', compact('orders'));
    }
}