<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;

class TokoController extends Controller
{
    public function index()
    {
        $toko = Toko::orderBy("idtoko", "asc")->get();
        $generator = new BarcodeGeneratorPNG();

        $toko->transform(function ($item) use ($generator) {
            $barcodeBiner = $generator->getBarcode($item->idtoko, $generator::TYPE_CODE_128);
            $item->barcode_base64 = base64_encode($barcodeBiner);
            return $item;
        });

        return view("admin.toko.index", compact('toko'));
    }

    public function create()
    {
        return view('admin.toko.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_toko'  => 'required|string|max:255',
            'latitude'   => 'required|numeric',
            'longtitude' => 'required|numeric',
            'accuracy'   => 'required|numeric',
        ]);

        $exists = Toko::where('nama_toko', $validated['nama_toko'])->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Toko dengan nama yang sama sudah ada.'], 422);
        }

        $result = Toko::create($validated);

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Toko berhasil ditambahkan!',
                'redirect' => route('admin.toko.index')
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Gagal menyimpan data.'], 500);
    }

    // public function edit($id)
    // {
    //     $toko = Toko::where('idtoko', $id)->firstOrFail();
    //     return view('admin.toko.edit', compact('toko'));
    // }

    // public function update(Request $request, $id)
    // {
    //     $validated = $request->validate([
    //         'nama_toko'  => 'required|string|max:255',
    //         'latitude'   => 'required|numeric',
    //         'longtitude' => 'required|numeric',
    //         'accuracy'   => 'required|numeric',
    //     ]);

    //     $toko = Toko::where('idtoko', $id)->update($validated);

    //     if ($toko) {
    //         return response()->json([
    //             'success' => true, 
    //             'message' => 'Toko berhasil diperbarui!',
    //             'redirect' => route('admin.toko.list')
    //         ]);
    //     }

    //     return response()->json(['success' => false, 'message' => 'Gagal memperbarui data.'], 500);
    // }

    // public function edit($id)
    // {
    //     // Menggunakan first() agar mendapatkan objek tunggal, bukan koleksi
    //     $toko = Toko::where('idtoko', $id)->firstOrFail();
    //     return view('admin.toko.edit', compact('toko'));
    // }

    public function edit($id)
    {
        $toko = Toko::where('idtoko', $id)->firstOrFail();
        return view('admin.toko.edit', compact('toko'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_toko'  => 'required|string|max:255',
            'latitude'   => 'required|numeric',
            'longtitude' => 'required|numeric',
            'accuracy'   => 'required|numeric',
        ]);

        $toko = Toko::findOrFail($id);

        $result = $toko->update($validated);

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Toko berhasil diperbarui!',
                'redirect' => route('admin.toko.index')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal memperbarui toko.'
        ], 500);
    }

    public function destroy($id)
    {
        $toko = Toko::findOrFail($id);
        if ($toko->delete()) {
            return response()->json(['success' => true, 'message' => 'Toko berhasil dihapus!']);
        }
        return response()->json(['success' => false, 'message' => 'Gagal menghapus toko.'], 500);
    }
}
