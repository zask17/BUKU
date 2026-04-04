<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Provinsi;
use App\Models\Kota;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
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

    public function indexAxios() {
        $layout = $this->getLayout();
        $provinsis = Provinsi::orderBy('name', 'asc')->get();
        return view('wilayah.index_axios', compact('provinsis', 'layout'));
    }

    public function indexAjax() {
        $layout = $this->getLayout();
        $provinsis = Provinsi::orderBy('name', 'asc')->get();
        return view('wilayah.index_ajax', compact('provinsis', 'layout'));
    }

    public function getKota(Request $request) {
        $data = Kota::where('province_id', $request->id)->orderBy('name', 'asc')->get();
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function getKecamatan(Request $request) {
        $data = Kecamatan::where('regency_id', $request->id)->orderBy('name', 'asc')->get();
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function getKelurahan(Request $request) {
        $data = Kelurahan::where('district_id', $request->id)->orderBy('name', 'asc')->get();
        return response()->json(['status' => 'success', 'data' => $data]);
    }
}