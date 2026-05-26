<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AntrianController extends Controller
{
    // =========================================================================
    // FUNGSI UTAMA: REFRESH DATA CACHE (DIPANGGIL SETIAP ADA PERUBAHAN DATA)
    // =========================================================================
    private function updateAntrianCache()
    {
        $hariIni = date('Y-m-d');

        // 1. DAFTAR TUNGGU (Hanya yang berstatus 'menunggu' untuk HARI INI)
        $daftar_tunggu = DB::table('antrian')
            ->join('poli', 'antrian.idpoli', '=', 'poli.idpoli')
            ->where('antrian.status', 'menunggu')
            ->whereDate('antrian.created_at', $hariIni)
            ->whereNull('antrian.deleted_at')
            ->select('antrian.*', 'poli.nama_poli', 'poli.kode_poli')
            ->orderBy('antrian.idantrian', 'asc')->get();

        // 2. SEDANG DIPANGGIL (Hanya yang aktif HARI INI)
        $sedang_dipanggil = DB::table('antrian')
            ->join('poli', 'antrian.idpoli', '=', 'poli.idpoli')
            ->where('antrian.status', 'dipanggil')
            ->whereDate('antrian.created_at', $hariIni)
            ->whereNull('antrian.deleted_at')
            ->select('antrian.*', 'poli.nama_poli', 'poli.kode_poli')
            ->orderBy('antrian.waktu_panggil', 'desc')->first();

        // 3. TERLEWAT HARI INI
        $terlewat = DB::table('antrian')
            ->join('poli', 'antrian.idpoli', '=', 'poli.idpoli')
            ->where('antrian.status', 'terlewat')
            ->whereDate('antrian.created_at', $hariIni)
            ->whereNull('antrian.deleted_at')
            ->select('antrian.*', 'poli.nama_poli', 'poli.kode_poli')
            ->orderBy('antrian.idantrian', 'desc')->get();

        // 4. DATA HARI LAIN (Semua riwayat tanggal sebelumnya yang tersimpan di sistem)
        $hari_lain = DB::table('antrian')
            ->join('poli', 'antrian.idpoli', '=', 'poli.idpoli')
            ->whereDate('antrian.created_at', '<', $hariIni)
            ->whereNull('antrian.deleted_at')
            ->select('antrian.*', 'poli.nama_poli', 'poli.kode_poli', DB::raw("TO_CHAR(antrian.created_at, 'DD-MM-YYYY') as tanggal_antrian"))
            ->orderBy('antrian.created_at', 'desc')
            ->orderBy('antrian.idantrian', 'asc')->get();

        $state = [
            'daftar_tunggu'    => $daftar_tunggu,
            'sedang_dipanggil' => $sedang_dipanggil,
            'terlewat'         => $terlewat,
            'hari_lain'        => $hari_lain
        ];

        // Shared state disimpan ke dalam Cache berdurasi 12 jam
        Cache::put('antrian_state', $state, now()->addHours(12));
    }

    // =========================================================================
    // 1. INTERFACES & REGISTRASI PASIEN (GUEST)
    // =========================================================================
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

        $idBaru = DB::table('antrian')->insertGetId([
            'nama'   => $request->nama,
            'idpoli' => $request->idpoli,
            'status' => 'menunggu'
        ], 'idantrian');

        // Segarkan data cache setiap kali ada penambahan pasien baru
        $this->updateAntrianCache();

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

    // =========================================================================
    // 2. PANEL MANAGEMENT CONTROL OPERATOR LOKET (ADMIN)
    // =========================================================================
    public function adminIndex()
    {
        $daftarPoli = DB::table('poli')->whereNull('deleted_at')->get();
        return view('antrian.admin', compact('daftarPoli'));
    }

    public function adminPanggil(Request $request)
    {
        $hariIni = date('Y-m-d');

        if ($request->has('idantrian') && $request->idantrian != null) {
            DB::table('antrian')
                ->where('status', 'dipanggil')
                ->whereDate('created_at', $hariIni)
                ->update(['status' => 'selesai', 'waktu_selesai' => now(), 'updated_at' => now()]);

            DB::table('antrian')
                ->where('idantrian', $request->idantrian)
                ->update(['status' => 'dipanggil', 'waktu_panggil' => now(), 'updated_at' => now()]);

            $this->updateAntrianCache();
            return response()->json(['success' => true, 'message' => 'Berhasil memanggil pasien terpilih.']);
        }

        $query = DB::table('antrian')
            ->where('status', 'menunggu')
            ->whereDate('created_at', $hariIni)
            ->whereNull('deleted_at');

        if ($request->filled('kode_poli')) {
            $query->whereIn('idpoli', function ($q) use ($request) {
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

        $this->updateAntrianCache();
        return response()->json(['success' => true, 'message' => 'Memanggil antrian berikutnya.']);
    }

    public function adminLewatkan(Request $request)
    {
        $request->validate(['idantrian' => 'required|integer']);

        DB::table('antrian')->where('idantrian', $request->idantrian)->update([
            'status'     => 'terlewat',
            'updated_at' => now()
        ]);

        $this->updateAntrianCache();
        return response()->json(['success' => true, 'message' => 'Pasien berhasil dilewatkan.']);
    }

    public function adminPanggilTerlewat(Request $request)
    {
        $request->validate(['idantrian' => 'required|integer']);
        $hariIni = date('Y-m-d');

        DB::table('antrian')
            ->where('status', 'dipanggil')
            ->whereDate('created_at', $hariIni)
            ->update(['status' => 'selesai', 'waktu_selesai' => now(), 'updated_at' => now()]);

        DB::table('antrian')->where('idantrian', $request->idantrian)->update([
            'status'        => 'dipanggil',
            'waktu_panggil' => now(),
            'updated_at'    => now()
        ]);

        $this->updateAntrianCache();
        return response()->json(['success' => true, 'message' => 'Memanggil ulang pasien terlewat.']);
    }

    public function papanIndex()
    {
        return view('antrian.papan');
    }

    // =========================================================================
    // 3. SSE STREAM ENGINE SINKRONISASI DATA CACHE (KONEKSI RINGAN & STABIL)
    // =========================================================================
    public function streamAntrian()
    {
        set_time_limit(0);
        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', 1);
        }
        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);

        return response()->stream(function () {
            $lastHash = '';

            // Pastikan membersihkan buffer keluaran PHP dari sisa proses sebelumnya
            while (ob_get_level() > 0) {
                @ob_end_clean();
            }

            if (session_id()) {
                session_write_close();
            }

            // Isi kondisi state awal cache saat pertama kali client melakukan listen
            $this->updateAntrianCache();

            while (true) {
                $state = Cache::get('antrian_state', [
                    'daftar_tunggu'    => [],
                    'sedang_dipanggil' => null,
                    'terlewat'         => [],
                    'hari_lain'        => []
                ]);

                $currentHash = md5(json_encode($state));

                if ($currentHash !== $lastHash) {
                    echo 'event: queue-update' . PHP_EOL;
                    echo 'data: ' . json_encode($state) . PHP_EOL;
                    echo PHP_EOL;
                    $lastHash = $currentHash;
                }

                echo ': keep-alive' . PHP_EOL . PHP_EOL;

                if (connection_aborted()) {
                    break;
                }

                @ob_flush();
                @flush();
                sleep(1); 
            }
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache, no-store, must-revalidate',
            'Connection'        => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function stream(Request $request = null)
    {
        return $this->streamAntrian();
    }
}