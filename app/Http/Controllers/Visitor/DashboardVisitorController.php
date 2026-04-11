<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardVisitorController extends Controller
{
    private function getStats()
    {
        return [
            'jumlahKategori' => DB::table('kategori')->count(),
            'jumlahBuku'     => DB::table('buku')->count(),
            'jumlahBukuTerbaru' => DB::table('buku')->orderBy('idbuku', 'desc')->count(),
        ];
    }

    public function index()
    {
        return view('visitor.dashboard-visitor', $this->getStats());
    }
}