<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Http; // Wajib untuk HTTP Client
use Illuminate\Support\Facades\DB; // Wajib untuk Query Builder
use Illuminate\Support\Facades\Auth;

class WilayahController extends Controller
{
    // Base URL dari API Wilayah Indonesia
    // private $baseUrl = 'https://www.emsifa.com/api-wilayah-indonesia/api';
    private $baseUrl = 'https://raw.githubusercontent.com/guzfirdaus/Wilayah-Administrasi-Indonesia/master/api';

    private function getLayout() {
        if (Auth::check()) {
            return (Auth::user()->idrole == 1) ? 'layouts.admin.main' : 'layouts.visitor.main';
        }
        return 'layouts.guest.main';
    }

// Menampilkan halaman awal dengan daftar Provinsi dari Database
    public function indexAxios() 
    {
        $layout = $this->getLayout(); 
        // Query Builder: Mengambil data dari tabel reg_provinces
        $provinsis = DB::table('reg_provinces')->orderBy('name', 'asc')->get();
        
        return view('wilayah.index_axios', compact('provinsis', 'layout'));
    }
public function indexAjax() 
    {
        $layout = $this->getLayout(); 
        $provinsis = DB::table('reg_provinces')->orderBy('name', 'asc')->get();
        
        return view('wilayah.index_ajax', compact('provinsis', 'layout'));
    }

// API: Mendapatkan Kota berdasarkan ID Provinsi
    public function getKota(Request $request) {
        // Query Builder: Mencari di reg_regencies berdasarkan province_id
        $data = DB::table('reg_regencies')
                  ->where('province_id', $request->id)
                  ->orderBy('name', 'asc')
                  ->get();

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data'   => $data
        ]);
    }

// API: Mendapatkan Kecamatan berdasarkan ID Kota
    public function getKecamatan(Request $request) {
        $data = DB::table('reg_districts')
                  ->where('regency_id', $request->id)
                  ->orderBy('name', 'asc')
                  ->get();

        return response()->json([
            'status' => 'success', 
            'code'   => 200,
            'data'   => $data
        ]);
    }
// API: Mendapatkan Kelurahan berdasarkan ID Kecamatan
    public function getKelurahan(Request $request) {
        $data = DB::table('reg_villages')
                  ->where('district_id', $request->id)
                  ->orderBy('name', 'asc')
                  ->get();

        return response()->json([
            'status' => 'success', 
            'code'   => 200,
            'data'   => $data
        ]);
    }
}