<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class WilayahController extends Controller
{
    // Base URL dari API Wilayah Indonesia
    private $baseUrl = 'https://www.emsifa.com/api-wilayah-indonesia/api';

    private function getLayout() {
        return (Auth::user()->idrole == 1) ? 'layouts.admin.main' : 'layouts.visitor.main';
    }

public function indexAxios() 
    {
        $layout = $this->getLayout(); 
        $response = Http::get("{$this->baseUrl}/provinces.json");
        $provinsis = $response->successful() ? json_decode($response->body()) : [];
        
        // Mengarah ke view axios
        return view('admin.wilayah.index_axios', compact('provinsis', 'layout'));
    }

    public function indexAjax() 
    {
        $layout = $this->getLayout(); 
        $response = Http::get("{$this->baseUrl}/provinces.json");
        $provinsis = $response->successful() ? json_decode($response->body()) : [];
        
        // Mengarah ke view ajax
        return view('admin.wilayah.index_ajax', compact('provinsis', 'layout'));
    }

    public function getKota(Request $request) {
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