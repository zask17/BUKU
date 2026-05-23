<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AntrianController extends Controller
{
    // ===================== INTERFACES & REGISTRASI GUEST =====================
    public function guestIndex()
    {
        $daftarPoli = DB::table('poli')->whereNull('deleted_at')->get();
        return view('antrian.guest', compact('daftarPoli'));
    }

    public function guestDaftar(Request $request)
    {
        $request->validate([
            'nama'   => 'required|string|max:150',
            'idpoli' => 'required|integer|exists:poli,idpoli'
        ]);

        // Mengandalkan PostgreSQL trigger BEFORE INSERT untuk kalkulasi reset harian nomor urut
        $idBaru = DB::table('antrian')->insertGetId([
            'nama'   => $request->nama,
            'idpoli' => $request->idpoli,
            'status' => 'menunggu'
        ], 'idantrian');

        $dataAntrian = DB::table('antrian')
            ->join('poli', 'antrian.idpoli', '=', 'poli.idpoli')
            ->where('antrian.idantrian', $idBaru)
            ->select('antrian.*', 'poli.nama_poli')
            ->first();

        return response()->json([
            'success'   => true,
            'nomor'     => $dataAntrian->nomor,
            'nama'      => $dataAntrian->nama,
            'nama_poli' => $dataAntrian->nama_poli
        ]);
    }

    // ===================== PANEL CONTROL OPERATOR ADMIN =====================
    public function adminIndex()
    {
        $daftarPoli = DB::table('poli')->whereNull('deleted_at')->get();
        return view('antrian.admin', compact('daftarPoli'));
    }

    public function adminPanggil(Request $request)
    {
        $hariIni = now()->format('Y-m-d');

        if ($request->has('idantrian') && $request->idantrian != null) {
            DB::table('antrian')
                ->where('status', 'dipanggil')
                ->whereDate('created_at', $hariIni)
                ->update(['status' => 'selesai', 'waktu_selesai' => now(), 'updated_at' => now()]);

            DB::table('antrian')
                ->where('idantrian', $request->idantrian)
                ->update(['status' => 'dipanggil', 'waktu_panggil' => now(), 'updated_at' => now()]);

            return response()->json(['success' => true, 'message' => 'Berhasil memanggil pasien terpilih.']);
        }

        $query = DB::table('antrian')
            ->where('status', 'menunggu')
            ->whereDate('created_at', $hariIni)
            ->whereNull('deleted_at');

        if ($request->filled('kode_poli')) {
            $query->whereIn('idpoli', function($q) use ($request) {
                $q->select('idpoli')->from('poli')->where('kode_poli', $request->kode_poli);
            });
        }

        $berikutnya = $query->orderBy('idantrian', 'asc')->first();

        if (!$berikutnya) {
            return response()->json(['success' => false, 'message' => 'Antrian tunggu hari ini sudah kosong.'], 404);
        }

        DB::table('antrian')
            ->where('status', 'dipanggil')
            ->whereDate('created_at', $hariIni)
            ->update(['status' => 'selesai', 'waktu_selesai' => now(), 'updated_at' => now()]);

        DB::table('antrian')
            ->where('idantrian', $berikutnya->idantrian)
            ->update(['status' => 'dipanggil', 'waktu_panggil' => now(), 'updated_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Memanggil antrian berikutnya.']);
    }

    public function adminLewatkan(Request $request)
    {
        $request->validate(['idantrian' => 'required|integer']);

        DB::table('antrian')->where('idantrian', $request->idantrian)->update([
            'status'     => 'terlewat',
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Pasien berhasil dilewatkan.']);
    }

    public function adminPanggilTerlewat(Request $request)
    {
        $request->validate(['idantrian' => 'required|integer']);
        $hariIni = now()->format('Y-m-d');

        DB::table('antrian')
            ->where('status', 'dipanggil')
            ->whereDate('created_at', $hariIni)
            ->update(['status' => 'selesai', 'waktu_selesai' => now(), 'updated_at' => now()]);

        DB::table('antrian')->where('idantrian', $request->idantrian)->update([
            'status'        => 'dipanggil',
            'waktu_panggil' => now(),
            'updated_at'    => now()
        ]);

        return response()->json(['success' => true]);
    }

    public function papanIndex()
    {
        return view('antrian.papan');
    }

    // ===================== ENGINE REAL-TIME SSE (STREAM) =====================
    public function stream()
    {
        set_time_limit(0); // 
        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', 1);
        }
        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);

        return response()->stream(function () {
            $lastHash = '';

            // Memutus siklus tumpukan buffer memory lokal server PHP di awal koneksi
            if (ob_get_level() > 0) {
                @ob_end_clean();
            }

            while (true) {
                $hariIni = now()->format('Y-m-d');

                $daftar_tunggu = DB::table('antrian')
                    ->join('poli', 'antrian.idpoli', '=', 'poli.idpoli')
                    ->where('antrian.status', 'menunggu')
                    ->whereDate('antrian.created_at', $hariIni)
                    ->whereNull('antrian.deleted_at')
                    ->select('antrian.*', 'poli.nama_poli')
                    ->orderBy('antrian.idantrian', 'asc')->get();

                $sedang_dipanggil = DB::table('antrian')
                    ->join('poli', 'antrian.idpoli', '=', 'poli.idpoli')
                    ->where('antrian.status', 'dipanggil')
                    ->whereDate('antrian.created_at', $hariIni)
                    ->whereNull('antrian.deleted_at')
                    ->select('antrian.*', 'poli.nama_poli')->first();

                $terlewat = DB::table('antrian')
                    ->join('poli', 'antrian.idpoli', '=', 'poli.idpoli')
                    ->where('antrian.status', 'terlewat')
                    ->whereDate('antrian.created_at', $hariIni)
                    ->whereNull('antrian.deleted_at')
                    ->select('antrian.*', 'poli.nama_poli')
                    ->orderBy('antrian.idantrian', 'desc')->get();

                $state = [
                    'daftar_tunggu'    => $daftar_tunggu,
                    'sedang_dipanggil' => $sedang_dipanggil,
                    'terlewat'         => $terlewat
                ];

                $currentHash = md5(json_encode($state));

                if ($currentHash !== $lastHash) {
                    echo "event: queue-update\n"; // [cite: 21, 29]
                    echo "data: " . json_encode($state) . "\n\n"; // [cite: 21, 22, 29]
                    $lastHash = $currentHash;
                }

                echo ": keep-alive\n\n"; // [cite: 21]

                if (connection_aborted()) { break; } // [cite: 29]

                // Paksa aliran data keluar melewati pembungkus buffer Apache/Nginx [cite: 29, 64]
                @ob_flush();
                @flush();
                
                usleep(500000); // interval kirim stream 0.5 detik
            }
        }, 200, [
            'Content-Type'      => 'text/event-stream', // [cite: 23, 29]
            'Cache-Control'     => 'no-cache, no-store, must-revalidate', // [cite: 29, 64]
            'Connection'        => 'keep-alive',
            'X-Accel-Buffering' => 'no', // [cite: 29, 64]
        ]);
    }
}