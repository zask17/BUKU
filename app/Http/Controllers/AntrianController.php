<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Poli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AntrianController extends Controller
{
    // ===================== GUEST =====================
    public function guestIndex()
    {
        $polis = Poli::whereNull('deleted_at')->get();
        return view('antrian.guest', compact('polis'));
    }


    public function guestDaftar(Request $request)
    {
        $request->validate([
            'nama'   => 'required|string|max:150',
            'idpoli' => 'required|exists:poli,idpoli'
        ]);

        $antrian = Antrian::create([
            'nama'   => $request->nama,
            'idpoli' => $request->idpoli,
        ]);

        // Refresh agar trigger terbaca
        $antrian->refresh();

        $successData = [
            'nama'  => $antrian->nama,
            'nomor' => $antrian->nomor,           // Pastikan ini terisi
            'poli'  => $antrian->poli->nama_poli ?? 'Poli Umum'
        ];

        return redirect()->back()->with('success_antrian', $successData);
    }

    // public function guestDaftar(Request $request)
    // {
    //     $request->validate([
    //         'nama'   => 'required|string|max:150',
    //         'idpoli' => 'required|exists:poli,idpoli'
    //     ]);

    //     $antrian = Antrian::create([
    //         'nama'   => $request->nama,
    //         'idpoli' => $request->idpoli,
    //     ]);

    //     $successData = [
    //         'nama'  => $antrian->nama,
    //         'nomor' => $antrian->nomor,
    //         'poli'  => $antrian->poli->nama_poli ?? ''
    //     ];

    //     // Trigger update SSE
    //     Cache::put('antrian_trigger_update', true, now()->addMinutes(10));

    //     return redirect()->back()->with('success_antrian', $successData);
    // }

    // ===================== ADMIN =====================
    public function adminIndex()
    {
        return view('antrian.admin');
    }

    public function panggilNext()
    {
        $antrian = Antrian::where('tanggal', today())
            ->where('status', 'waiting')
            ->orderBy('nomor_harian')
            ->first();

        if (!$antrian) {
            return response()->json(['status' => 'empty', 'message' => 'Antrian hari ini kosong!']);
        }

        $antrian->update([
            'status'       => 'calling',
            'waktu_panggil' => now()
        ]);

        Cache::put('antrian_sekarang', $antrian->fresh(), now()->addMinutes(10));
        Cache::put('antrian_trigger_update', true);

        return response()->json(['status' => 'success', 'data' => $antrian]);
    }

    public function lewatkanAntrian()
    {
        $antrianSekarang = Cache::get('antrian_sekarang');

        if (!$antrianSekarang) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada antrian yang sedang dipanggil']);
        }

        // Update di database
        Antrian::where('idantrian', $antrianSekarang['idantrian'] ?? $antrianSekarang->idantrian ?? null)
            ->update(['status' => 'skipped']);

        Cache::forget('antrian_sekarang');
        Cache::put('antrian_trigger_update', true);

        return response()->json(['status' => 'success']);
    }

    public function panggilTerlewat(Request $request)
    {
        $request->validate(['nomor' => 'required|integer']);

        $antrian = Antrian::where('tanggal', today())
            ->where('nomor', $request->nomor)
            ->where('status', 'skipped')
            ->first();

        if (!$antrian) {
            return response()->json(['status' => 'error', 'message' => 'Antrian terlewat tidak ditemukan']);
        }

        $antrian->update([
            'status'       => 'calling',
            'waktu_panggil' => now()
        ]);

        Cache::put('antrian_sekarang', $antrian->fresh(), now()->addMinutes(10));
        Cache::put('antrian_trigger_update', true);

        return response()->json(['status' => 'success']);
    }

    // ===================== PAPAN =====================
    public function papanIndex()
    {
        return view('antrian.papan');
    }

    // ===================== SSE STREAM =====================
    public function stream()
    {
        set_time_limit(0);

        return response()->stream(function () {
            while (true) {
                if (Cache::get('antrian_trigger_update')) {
                    Cache::forget('antrian_trigger_update');
                }

                $antrianSekarang = Cache::get('antrian_sekarang');

                // Ambil data hari ini
                $antrianList = Antrian::where('tanggal', today())
                    ->where('status', 'waiting')
                    ->orderBy('nomor_harian')
                    ->get(['idantrian', 'nomor', 'nama', 'waktu_masuk as waktu']);

                $antrianTerlewat = Antrian::where('tanggal', today())
                    ->where('status', 'skipped')
                    ->orderBy('nomor_harian')
                    ->get(['idantrian', 'nomor', 'nama']);

                $data = [
                    'antrian_list'     => $antrianList,
                    'antrian_terlewat' => $antrianTerlewat,
                    'antrian_sekarang' => $antrianSekarang,
                ];

                echo "event: queue-update" . PHP_EOL;
                echo "data: " . json_encode($data) . PHP_EOL . PHP_EOL;

                ob_flush();
                flush();

                if (connection_aborted()) break;

                sleep(1);
            }
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
