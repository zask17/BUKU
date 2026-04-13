<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function index() {
        $customers = Customer::all();
        return view('customer.index', compact('customers'));
    }

    public function create1() { return view('customer.create1'); } // Versi BLOB
    public function create2() { return view('customer.create2'); } // Versi Path

    public function store1(Request $request) {
        $img = $request->image; // Data URL dari webcam
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);

        Customer::create([
            'nama_customer' => $request->nama,
            'foto_blob' => $data
        ]);
        return redirect()->route('customer.index');
    }

    public function store2(Request $request) {
        $img = $request->image;
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);

        $fileName = time() . '.png';
        Storage::disk('public')->put('customers/' . $fileName, $data);

        Customer::create([
            'nama_customer' => $request->nama,
            'foto_path' => 'customers/' . $fileName
        ]);
        return redirect()->route('customer.index');
    }
}