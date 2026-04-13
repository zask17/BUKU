<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Provinsi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::orderBy('idcustomer', 'asc')->get();
        return view('admin.customer.index', compact('customers'));
    }

    public function create1()
    {
        $provinsis = Provinsi::orderBy('name', 'asc')->get();
        return view('admin.customer.create1', compact('provinsis'));
    }

    public function create2()
    {
        $provinsis = Provinsi::orderBy('name', 'asc')->get();
        return view('admin.customer.create2', compact('provinsis'));
    }

    public function store1(Request $request)
    {
        $request->validate([
            'nama'         => 'required|string|max:255',
            'image'        => 'required',
            'id_provinsi'  => 'required',
            'id_kota'      => 'required',
            'id_kecamatan' => 'required',
            'id_kelurahan' => 'required',
            'kode_pos'     => 'required|string|size:5',
        ]);

        try {
            $img = $request->image;
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $binaryData = base64_decode($img, true);

            $hexData = '\\x' . bin2hex($binaryData);

            Customer::create([
                'nama_customer' => $request->nama,
                'alamat'        => $request->alamat,
                'id_provinsi'   => $request->id_provinsi,
                'id_kota'       => $request->id_kota,
                'id_kecamatan'  => $request->id_kecamatan,
                'id_kelurahan'  => $request->id_kelurahan,
                'kode_pos'      => $request->kode_pos,
                'foto_blob'     => $hexData,
            ]);

            return redirect()->route('admin.customer.index')->with('success', 'Customer berhasil ditambah (BLOB)');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }

    public function store2(Request $request)
    {
        $request->validate([
            'nama'         => 'required|string|max:255',
            'image'        => 'required',
            'id_provinsi'  => 'required',
            'id_kota'      => 'required',
            'id_kecamatan' => 'required',
            'id_kelurahan' => 'required',
            'kode_pos'     => 'required|string|size:5',
        ]);

        try {
            $img = $request->image;
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $binaryData = base64_decode($img, true);

            $fileName = 'cust_' . time() . '_' . rand(1000, 9999) . '.png';
            Storage::disk('public')->put('customers/' . $fileName, $binaryData);

            Customer::create([
                'nama_customer' => $request->nama,
                'alamat'        => $request->alamat,
                'id_provinsi'   => $request->id_provinsi,
                'id_kota'       => $request->id_kota,
                'id_kecamatan'  => $request->id_kecamatan,
                'id_kelurahan'  => $request->id_kelurahan,
                'kode_pos'      => $request->kode_pos,
                'foto_path'     => 'customers/' . $fileName,
            ]);

            return redirect()->route('admin.customer.index')->with('success', 'Customer berhasil ditambah (File)');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        $provinsis = Provinsi::orderBy('name', 'asc')->get();
        return view('admin.customer.edit', compact('customer', 'provinsis'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'nama_customer' => 'required|string|max:255',
            'kode_pos'      => 'required|string|size:5',
            'id_provinsi'   => 'required',
            'id_kota'       => 'required',
            'id_kecamatan'  => 'required',
            'id_kelurahan'  => 'required',
        ]);

        $dataUpdate = [
            'nama_customer' => $request->nama_customer,
            'alamat'        => $request->alamat,
            'id_provinsi'   => $request->id_provinsi,
            'id_kota'       => $request->id_kota,
            'id_kecamatan'  => $request->id_kecamatan,
            'id_kelurahan'  => $request->id_kelurahan,
            'kode_pos'      => $request->kode_pos,
        ];

        if ($request->filled('image')) {
            $img = $request->image;
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $binaryData = base64_decode($img);

            if ($customer->foto_path) {
                Storage::disk('public')->delete($customer->foto_path);
                $dataUpdate['foto_path'] = null;
            }

            $dataUpdate['foto_blob'] = '\\x' . bin2hex($binaryData);
        }

        $customer->update($dataUpdate);

        return redirect()->route('admin.customer.index')->with('success', 'Data customer berhasil diperbarui');
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        if ($customer->foto_path) {
            Storage::disk('public')->delete($customer->foto_path);
        }
        $customer->delete();
        return redirect()->route('admin.customer.index')->with('success', 'Customer berhasil dihapus');
    }
}