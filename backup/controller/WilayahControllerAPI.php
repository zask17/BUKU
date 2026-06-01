<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class WilayahControllerAPI extends Controller
{
    // Base URL dari API Wilayah Indonesia
    // private $baseUrl = 'https://www.emsifa.com/api-wilayah-indonesia/api';
    private $baseUrl = 'https://raw.githubusercontent.com/guzfirdaus/Wilayah-Administrasi-Indonesia/master/api';

    private function getLayout() {
        return (Auth::user()->idrole == 1) ? 'layouts.admin.main' : 'layouts.visitor.main';
    }

// Menampilkan halaman dengan daftar Provinsi awal
    public function indexAxios() 
    {
        $layout = $this->getLayout(); 
        $response = Http::get("{$this->baseUrl}/provinces.json");
        $provinsis = $response->successful() ? $response->json() : [];
        
        return view('admin.wilayah.index_axios', compact('provinsis', 'layout'));
    }

public function indexAjax() 
    {
        $layout = $this->getLayout(); 
        $response = Http::get("{$this->baseUrl}/provinces.json");
        $provinsis = $response->successful() ? $response->json() : [];
        
        return view('admin.wilayah.index_ajax', compact('provinsis', 'layout'));
    }

// API: get Kota berdasarkan ID Provinsi
    public function getKota(Request $request) {
        // Endpoint GitHub: regencies/{id_provinsi}.json
        $response = Http::get("{$this->baseUrl}/regencies/{$request->id}.json");
        return response()->json([
            'status' => 'success',
            'data'   => $response->json()
        ]);
    }

    public function getKecamatan(Request $request) {
        $response = Http::get("{$this->baseUrl}/districts/{$request->id}.json");
        return response()->json([
            'status' => 'success', 
            'data'   => $response->json()
        ]);
    }

    public function getKelurahan(Request $request) {
        $response = Http::get("{$this->baseUrl}/villages/{$request->id}.json");
        return response()->json([
            'status' => 'success', 
            'data'   => $response->json()
        ]);
    }
}