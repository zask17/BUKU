<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\Toko; // Gunakan model Toko
// use App\Models\Sales; // Gunakan model Sales
// use Illuminate\Support\Facades\Auth;

// class SalesController extends Controller
// {
//     public function index()
//     {
//         // Ganti 'lokasi_toko' menjadi model Toko
//         $listToko = Toko::all();
//         return view('sales.dashboard-sales', compact('listToko'));
//     }

//     /**
//      * Lookup toko berdasarkan barcode (idtoko)
//      * Digunakan oleh AJAX saat sales scan barcode
//      */
//     public function findByBarcode($id)
//     {
//         $toko = Toko::where('idtoko', $id)->first();

//         if (!$toko) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Toko tidak ditemukan atau barcode tidak valid.'
//             ]);
//         }

//         return response()->json([
//             'success' => true,
//             'message' => 'Toko ditemukan',
//             'data' => $toko
//         ]);
//     }

//     public function storeVisit(Request $request)
//     {
//         $validated = $request->validate([
//             'barcode'    => 'required',
//             'sales_lat'  => 'required|numeric',
//             'sales_long' => 'required|numeric',
//             'sales_acc'  => 'required|numeric',
//         ]);

//         $toko = Toko::where('idtoko', $validated['barcode'])->first();

//         if (!$toko) {
//             return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan!'], 404);
//         }

//         // Hitung jarak (Formula Haversine) [cite: 18, 76]
//         $jarakAktual = $this->haversine(
//             (float) $validated['sales_lat'],
//             (float) $validated['sales_long'],
//             (float) $toko->latitude,
//             (float) $toko->longtitude //
//         );

//         $radiusMax = 300; // [cite: 88]
//         $thresholdEfektif = $radiusMax + (float) $toko->accuracy + (float) $validated['sales_acc']; // [cite: 89]
//         $isAccepted = $jarakAktual <= $thresholdEfektif; // [cite: 90]

//         // PERBAIKAN DI SINI:
//         // Gunakan (int) round($jarakAktual) agar tidak error di PostgreSQL jika kolomnya integer
//         Sales::create([
//             'idtoko'    => $toko->idtoko,
//             'latitude'  => $validated['sales_lat'],
//             'longitude' => $validated['sales_long'],
//             'accuracy'  => $validated['sales_acc'],
//             'jarak'     => (int) round($jarakAktual), // Dibulatkan ke satuan meter terdekat
//             'status'    => $isAccepted ? 'DITERIMA' : 'DITOLAK',
//             'waktu'     => now(),
//         ]);

//         return response()->json([
//             'status'  => $isAccepted ? 'success' : 'error',
//             'message' => $isAccepted
//                 ? 'Kunjungan DITERIMA. Jarak: ' . round($jarakAktual, 2) . 'm'
//                 : 'Kunjungan DITOLAK. Jarak: ' . round($jarakAktual, 2) . 'm'
//         ]);
//     }

    // public function storeVisit(Request $request)
    // {
    //     // Validasi dan ambil data secara eksplisit untuk menghilangkan peringatan "Undefined property"
    //     $validated = $request->validate([
    //         'barcode'    => 'required',
    //         'sales_lat'  => 'required|numeric',
    //         'sales_long' => 'required|numeric',
    //         'sales_acc'  => 'required|numeric',
    //     ]);

    //     // Mengambil data dari hasil validasi agar lebih aman
    //     $barcode   = $validated['barcode'];
    //     $salesLat  = (float) $validated['sales_lat'];
    //     $salesLong = (float) $validated['sales_long'];
    //     $salesAcc  = (float) $validated['sales_acc'];

    //     // Cari toko berdasarkan idtoko
    //     $toko = Toko::where('idtoko', $barcode)->first();

    //     if (!$toko) {
    //         return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan!'], 404);
    //     }

    //     // Hitung jarak (Formula Haversine)
    //     // Gunakan casting float untuk memastikan tipe data benar
    //     $jarakAktual = $this->haversine(
    //         $salesLat, 
    //         $salesLong,
    //         (float) $toko->latitude, 
    //         (float) $toko->longtitude // Mengikuti kolom 'longtitude' di model Toko Anda
    //     );

    //     // Threshold: Radius 300m + Akurasi Toko + Akurasi Sales [cite: 89]
    //     $radiusMax = 300; 
    //     $thresholdEfektif = $radiusMax + (float) $toko->accuracy + $salesAcc;

    //     $isAccepted = $jarakAktual <= $thresholdEfektif;

    //     // Simpan ke tabel sales menggunakan model Sales
    //     Sales::create([
    //         'idtoko'    => $toko->idtoko,
    //         'latitude'  => $salesLat,
    //         'longitude' => $salesLong,
    //         'accuracy'  => $salesAcc,
    //         'jarak'     => $jarakAktual,
    //         'status'    => $isAccepted ? 'DITERIMA' : 'DITOLAK',
    //         'waktu'     => now(),
    //     ]);

    //     return response()->json([
    //         'status'  => $isAccepted ? 'success' : 'error',
    //         'message' => $isAccepted 
    //             ? 'Kunjungan DITERIMA. Jarak: ' . round($jarakAktual, 2) . 'm'
    //             : 'Kunjungan DITOLAK. Jarak: ' . round($jarakAktual, 2) . 'm (Maks: '.round($thresholdEfektif).'m)'
    //     ]);
    // }

    /**
     * Menambahkan Type Hinting (float) untuk menghilangkan peringatan "No type information"
     */
    // private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    // {
    //     $R = 6371000;
    //     $dLat = deg2rad($lat2 - $lat1);
    //     $dLng = deg2rad($lng2 - $lng1);
    //     $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
    //     $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    //     return (float) ($R * $c);
    // }
    // public function storeVisit(Request $request)
    // {
    //     $request->validate([
    //         'barcode' => 'required',
    //         'sales_lat' => 'required',
    //         'sales_long' => 'required',
    //         'sales_acc' => 'required',
    //     ]);

    //     // Cari toko berdasarkan idtoko (barcode di sini diasumsikan sebagai idtoko atau primary key)
    //     $toko = Toko::where('idtoko', $request->barcode)->first();

    //     if (!$toko) {
    //         return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan!']);
    //     }

    //     // Hitung jarak (Formula Haversine)
    //     $jarakAktual = $this->haversine(
    //         $request->sales_lat, $request->sales_long,
    //         $toko->latitude, $toko->longtitude // Perhatikan typo 'longtitude' di model Toko Anda
    //     );

    //     // Threshold: Radius 300m + Akurasi Toko + Akurasi Sales [cite: 89]
    //     $radiusMax = 300; 
    //     $thresholdEfektif = $radiusMax + $toko->accuracy + $request->sales_acc;

    //     $isAccepted = $jarakAktual <= $thresholdEfektif;

    //     // Simpan ke tabel sales menggunakan model Sales
    //     Sales::create([
    //         'idtoko'    => $toko->idtoko,
    //         'latitude'  => $request->sales_lat,
    //         'longitude' => $request->sales_long,
    //         'accuracy'  => $request->sales_acc,
    //         'jarak'     => $jarakAktual,
    //         'status'    => $isAccepted ? 'DITERIMA' : 'DITOLAK',
    //         'waktu'     => now(),
    //     ]);

    //     if ($isAccepted) {
    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Kunjungan DITERIMA. Jarak: ' . round($jarakAktual, 2) . 'm'
    //         ]);
    //     } else {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Kunjungan DITOLAK. Jarak: ' . round($jarakAktual, 2) . 'm (Maks: '.round($thresholdEfektif).'m)'
    //         ]);
    //     }
    // }

    // private function haversine($lat1, $lng1, $lat2, $lng2)
    // {
    //     $R = 6371000; 
    //     $dLat = deg2rad($lat2 - $lat1);
    //     $dLng = deg2rad($lng2 - $lng1);
    //     $a = sin($dLat/2)**2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2)**2;
    //     $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    //     return $R * $c;
    // }
// }

// <?php

// namespace App\Http\Controllers;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use App\Models\Sales;
// use App\Models\Toko;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\DB;

// class SalesController extends Controller
// {
//     public function dashboard()
//     {
//         $riwayat = Sales::with('toko')
//                         ->orderBy('waktu', 'desc')
//                         ->take(10)
//                         ->get();
//         return view('sales.dashboard-sales', compact('riwayat'));
//     }

//     public function findByBarcode($id)
//     {
//         $toko = Toko::where('idtoko', $id)->first();

//         if ($toko) {
//             return response()->json([
//                 'success' => true,
//                 'data' => $toko
//             ]);
//         }

//         return response()->json([
//             'success' => false,
//             'message' => 'Toko tidak ditemukan atau barcode tidak valid.'
//         ]);
//     }

//     public function store(Request $request)
//     {
//         $request->validate([
//             'idtoko'    => 'required|integer',
//             'latitude'  => 'required|numeric',
//             'longitude' => 'required|numeric',
//             'accuracy'  => 'required|numeric',
//             'jarak'     => 'required|numeric',
//             'status'    => 'required|string|in:diterima,ditolak',
//         ]);

//         try {
//             $sales = Sales::create([
//                 'idtoko'    => $request->idtoko,
//                 'latitude'  => $request->latitude,
//                 'longitude' => $request->longitude,
//                 'accuracy'  => $request->accuracy,
//                 'jarak'     => $request->jarak,
//                 'status'    => $request->status,
//                 'waktu'     => now(),
//             ]);

//             return response()->json([
//                 'success' => true,
//                 'message' => 'Kunjungan berhasil dicatat.'
//             ]);
//         } catch (\Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Gagal menyimpan data: ' . $e->getMessage()
//             ], 500);
//         }
//     }
// }