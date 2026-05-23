<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Poli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AntrianController extends Controller
{
    // ===================== GUEST =====================
    // public function guestIndex()
    // {
    //     $polis = Poli::whereNull('deleted_at')->get();
    //     return view('antrian.guest', compact('polis'));
    // }

    // public function guestDaftar(Request $request)
    // {
    //     $request->validate([
    //         'nama'   => 'required|string|max:150',
    //         'idpoli' => 'required|exists:poli,idpoli'
    //     ]);

    //     $antrian = Antrian::create([
    //         'nama'   => $request->nama,
    //         'idpoli' => $request->idpoli,
    //         'status' => 'waiting'
    //     ]);

    //     $antrian->refresh();

    //     $successData = [
    //         'nama'  => $antrian->nama,
    //         'nomor' => $antrian->nomor,           
    //         'poli'  => $antrian->poli->nama_poli ?? 'Poli Umum'
    //     ];

    //     // Memicu update SSE global
    //     Cache::put('antrian_trigger_update', true, now()->addMinutes(10));

    //     return redirect()->back()->with('success_antrian', $successData);
    // }

    // ===================== ADMIN =====================
    // public function adminIndex(Request $request)
    // {
    //     // Ambil semua poli untuk dropdown pilihan di view admin
    //     $polis = Poli::whereNull('deleted_at')->orderBy('nama_poli')->get();
        
    //     // Poli yang sedang aktif dipilih oleh admin (default diambil dari yang pertama jika belum milih)
    //     $selectedPoliId = $request->get('idpoli', $polis->first()->idpoli ?? null);

    //     return view('antrian.admin', compact('polis', 'selectedPoliId'));
    // }

    // public function panggilNext(Request $request)
    // {
    //     $request->validate(['idpoli' => 'required|exists:poli,idpoli']);
    //     $idpoli = $request->idpoli;

    //     // Ambil antrian waiting terkecil berdasarkan poli yang dipilih
    //     $antrian = Antrian::where('tanggal', today())
    //         ->where('idpoli', $idpoli)
    //         ->where('status', 'waiting')
    //         ->orderBy('nomor_harian', 'asc')
    //         ->first();

    //     if (!$antrian) {
    //         return response()->json([
    //             'status' => 'empty', 
    //             'message' => 'Antrian untuk poli ini sudah kosong!'
    //         ]);
    //     }

    //     $antrian->update([
    //         'status'        => 'calling',
    //         'waktu_panggil' => now()
    //     ]);

    //     // Simpan cache spesifik per poli agar tidak tercampur antar poli
    //     Cache::put("antrian_sekarang_poli_{$idpoli}", $antrian->fresh(), now()->addMinutes(10));
    //     Cache::put('antrian_trigger_update', true, now()->addMinutes(10));

    //     return response()->json(['status' => 'success', 'data' => $antrian]);
    // }

    // public function lewatkanAntrian(Request $request)
    // {
    //     $request->validate(['idpoli' => 'required|exists:poli,idpoli']);
    //     $idpoli = $request->idpoli;

    //     $antrianSekarang = Cache::get("antrian_sekarang_poli_{$idpoli}");

    //     if (!$antrianSekarang) {
    //         return response()->json(['status' => 'error', 'message' => 'Tidak ada antrian aktif di poli ini']);
    //     }

    //     $idAntrian = $antrianSekarang['idantrian'] ?? $antrianSekarang->idantrian ?? null;

    //     if ($idAntrian) {
    //         Antrian::where('idantrian', $idAntrian)->update(['status' => 'skipped']);
    //     }

    //     Cache::forget("antrian_sekarang_poli_{$idpoli}");
    //     Cache::put('antrian_trigger_update', true, now()->addMinutes(10));

    //     return response()->json(['status' => 'success']);
    // }

    // public function panggilTerlewat(Request $request)
    // {
    //     $request->validate([
    //         'nomor'  => 'required|integer',
    //         'idpoli' => 'required|exists:poli,idpoli'
    //     ]);
        
    //     $idpoli = $request->idpoli;

    //     $antrian = Antrian::where('tanggal', today())
    //         ->where('idpoli', $idpoli)
    //         ->where('nomor', $request->nomor)
    //         ->where('status', 'skipped')
    //         ->first();

    //     if (!$antrian) {
    //         return response()->json(['status' => 'error', 'message' => 'Antrian tidak ditemukan']);
    //     }

    //     $antrian->update([
    //         'status'        => 'calling',
    //         'waktu_panggil' => now()
    //     ]);

    //     Cache::put("antrian_sekarang_poli_{$idpoli}", $antrian->fresh(), now()->addMinutes(10));
    //     Cache::put('antrian_trigger_update', true, now()->addMinutes(10));

    //     return response()->json(['status' => 'success']);
    // }

    // ===================== PAPAN =====================
    // public function papanIndex(Request $request)
    // {
    //     $polis = Poli::whereNull('deleted_at')->orderBy('nama_poli')->get();
    //     $selectedPoliId = $request->get('idpoli', $polis->first()->idpoli ?? null);
        
    //     $poliAktif = Poli::find($selectedPoliId);

    //     return view('antrian.papan', compact('polis', 'selectedPoliId', 'poliAktif'));
    // }

    // ===================== SSE STREAM =====================
//     public function stream(Request $request)
//     {
//         set_time_limit(0);
//         // Tangkap idpoli dari query string stream (misal: /antrian/stream?idpoli=2)
//         $idpoli = $request->get('idpoli');

//         return response()->stream(function () use ($idpoli) {
//             while (true) {
//                 if (Cache::get('antrian_trigger_update')) {
//                     Cache::forget('antrian_trigger_update');
//                 }

//                 // Query bersyarat: Jika parameter idpoli diisi, filter hanya poli tersebut. Jika kosong, ambil semua.
//                 $antrianListQuery = Antrian::where('tanggal', today())->where('status', 'waiting');
//                 $antrianTerlewatQuery = Antrian::where('tanggal', today())->where('status', 'skipped');

//                 if ($idpoli) {
//                     $antrianListQuery->where('idpoli', $idpoli);
//                     $antrianTerlewatQuery->where('idpoli', $idpoli);
//                     $antrianSekarang = Cache::get("antrian_sekarang_poli_{$idpoli}");
//                 } else {
//                     // Fallback jika tidak memfilter poli (mengambil data terakhir global)
//                     $antrianSekarang = null; 
//                 }

//                 $antrianList = $antrianListQuery->orderBy('nomor_harian')->get(['idantrian', 'nomor', 'nama', 'waktu_masuk as waktu']);
//                 $antrianTerlewat = $antrianTerlewatQuery->orderBy('nomor_harian')->get(['idantrian', 'nomor', 'nama']);

//                 $data = [
//                     'antrian_list'     => $antrianList,
//                     'antrian_terlewat' => $antrianTerlewat,
//                     'antrian_sekarang' => $antrianSekarang,
//                 ];

//                 echo "event: queue-update" . PHP_EOL;
//                 echo "data: " . json_encode($data) . PHP_EOL . PHP_EOL;

//                 ob_flush();
//                 flush();

//                 if (connection_aborted()) break;

//                 sleep(1);
//             }
//         }, 200, [
//             'Content-Type'      => 'text/event-stream',
//             'Cache-Control'     => 'no-cache',
//             'X-Accel-Buffering' => 'no',
//         ]);
//     }
}