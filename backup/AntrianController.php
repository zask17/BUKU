<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AntrianController extends Controller
{
    public function guestIndex()
    {
        return view('antrian.guest');
    }

    public function guestDaftar(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100'
        ]);

        $currentNumber = Cache::get('antrian_terakhir_no', 0) + 1;
        Cache::put('antrian_terakhir_no', $currentNumber);

        $antrianBaru = [
            'nomor' => $currentNumber,
            'nama' => $request->nama,
            'waktu' => now()->format('H:i:s')
        ];

        $list = Cache::get('antrian_list', []);
        $list[] = $antrianBaru;
        Cache::put('antrian_list', $list);

        // Trigger update ke SSE
        Cache::put('antrian_trigger_update', true);

        return redirect()->back()->with('success_antrian', $antrianBaru);
    }

    public function adminIndex()
    {
        return view('antrian.admin');
    }

    public function panggilNext()
    {
        $list = Cache::get('antrian_list', []);

        if (count($list) > 0) {
            $sekarang = array_shift($list);
            Cache::put('antrian_list', $list);
            
            $sekarang['status'] = 'calling';
            $sekarang['timestamp'] = time();
            Cache::put('antrian_sekarang', $sekarang);
            Cache::put('antrian_trigger_update', true);

            return response()->json(['status' => 'success', 'data' => $sekarang]);
        }

        return response()->json(['status' => 'empty', 'message' => 'Antrian kosong!']);
    }

    public function lewatkanAntrian()
    {
        $sekarang = Cache::get('antrian_sekarang');
        
        if ($sekarang) {
            $terlewat = Cache::get('antrian_terlewat', []);
            $terlewat[] = [
                'nomor' => $sekarang['nomor'],
                'nama' => $sekarang['nama']
            ];
            Cache::put('antrian_terlewat', $terlewat);
            Cache::forget('antrian_sekarang');
            Cache::put('antrian_trigger_update', true);

            return response()->json(['status' => 'success']);
        }
        return response()->json(['status' => 'error', 'message' => 'Tidak ada antrian aktif yang bisa dilewatkan.']);
    }

    public function panggilTerlewat(Request $request)
    {
        $nomor = $request->nomor;
        $terlewat = Cache::get('antrian_terlewat', []);
        
        $key = array_search($nomor, array_column($terlewat, 'nomor'));
        
        if ($key !== false) {
            $panggilKembali = $terlewat[$key];
            unset($terlewat[$key]);
            Cache::put('antrian_terlewat', array_values($terlewat));

            $panggilKembali['status'] = 'calling';
            $panggilKembali['timestamp'] = time();
            Cache::put('antrian_sekarang', $panggilKembali);
            Cache::put('antrian_trigger_update', true);

            return response()->json(['status' => 'success']);
        }
        return response()->json(['status' => 'error', 'message' => 'Nomor tidak ditemukan di daftar terlewat.']);
    }

    public function papanIndex()
    {
        return view('antrian.papan');
    }

    // --- ENDPOINT SERVER-SENT EVENTS (SSE) STREAM ---
    public function stream(Request $request)
    {
        set_time_limit(0);

        return response()->stream(function () {
            while (true) {
                $data = [
                    'antrian_list'     => Cache::get('antrian_list', []),
                    'antrian_terlewat' => Cache::get('antrian_terlewat', []),
                    'antrian_sekarang' => Cache::get('antrian_sekarang', null),
                ];

                echo "event: queue-update" . PHP_EOL;
                echo "data: " . json_encode($data) . PHP_EOL;
                echo PHP_EOL;

                ob_flush();
                flush();

                if (connection_aborted()) {
                    break;
                }

                sleep(1);
            }
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}