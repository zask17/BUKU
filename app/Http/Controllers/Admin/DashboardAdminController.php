<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Kategori;
use App\Models\Buku;

class DashboardAdminController extends Controller
{
    /**
     * Fungsi helper mengumpulkan semua data statistik dari database asli
     */
    private function getStats()
    {
        // 1. Ambil data angka dasar (Statistik Atas)
        $jumlahBuku = Buku::count();
        $totalBuku = $jumlahBuku > 0 ? $jumlahBuku : 1;

        // 2. Ambil statistik kategori untuk Doughnut Chart (Data Real dari Tabel Kategori & Buku)
        $distribusiKategori = Kategori::withCount('buku')->get()->map(function ($item) use ($totalBuku) {
            return [
                'label' => $item->nama_kategori,
                'count' => $item->buku_count,
                'percentage' => round(($item->buku_count / $totalBuku) * 100, 1)
            ];
        });

        // 3. Ambil Aktivitas Terbaru (Data Real dari pendaftaran User terbaru)
        $aktivitasTerbaru = User::latest()->take(5)->get();

        // 4. Data Real untuk Line Chart (Kunjungan & Peminjaman)
        // Mengambil data transaksi penjualan 7 hari terakhir sebagai representasi kunjungan/aktivitas
        $statsHarian = DB::table('penjualan')
            ->select(DB::raw('DATE(timestamp) as tanggal'), DB::raw('count(*) as total_kunjungan'), DB::raw('SUM(total) as total_nilai'))
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'desc')
            ->take(7)
            ->get()
            ->reverse();

        // Mengambil data peminjaman/detail item yang terjual sebagai representasi peminjaman
        $peminjamanHarian = DB::table('penjualan_detail')
            ->join('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id_penjualan')
            ->select(DB::raw('DATE(penjualan.timestamp) as tanggal'), DB::raw('SUM(jumlah) as total_item'))
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'desc')
            ->take(7)
            ->get()
            ->reverse();

        return [
            'jumlahPengguna'      => User::count(),
            'jumlahKategori'      => Kategori::count(),
            'jumlahBuku'          => $jumlahBuku,
            'jumlahAdmin'         => User::where('idrole', 1)->count(),
            'jumlahVisitor'       => User::where('idrole', 2)->count(),
            'distribusiKategori'  => $distribusiKategori,
            'aktivitasTerbaru'    => $aktivitasTerbaru,
            // Data Chart Real dari tabel penjualan
            'dataChartKunjungan'  => $statsHarian->pluck('total_kunjungan')->toArray(),
            'dataChartPeminjaman' => $peminjamanHarian->pluck('total_item')->toArray(),
            'labelsChart'         => $statsHarian->map(function($item) { 
                                        return date('d M', strtotime($item->tanggal)); 
                                     })->toArray(),
        ];
    }

    public function index()
    {
        return view('admin.dashboard-admin', $this->getStats());
    }
}