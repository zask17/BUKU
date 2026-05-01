<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
// use Picqer\Barcode\BarcodeGeneratorHTML;

class BarangBaruController extends Controller
{

    public function barangBaru()
    {
        return view('admin.barang.barang_baru');
    }

    public function barangBaruDatatable()
    {
        return view('admin.barang.barang_baru_datatable');
    }

}
