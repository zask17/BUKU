<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Kategori;
use App\Models\Buku;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DashboardAdminController extends Controller
{
    private function getStats()
    {
        return [
            'jumlahPengguna' => DB::table('users')->count(),
            'jumlahKategori' => DB::table('kategori')->count(),
            'jumlahBuku' => DB::table('buku')->count(),
        ];
    }

    public function index()
    {
        return view('admin.dashboard-admin', $this->getStats());
    }
}
