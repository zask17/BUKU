<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NfcCard;
use App\Models\Attendance;
use Carbon\Carbon;

class NFCController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with(['student.user'])
            ->whereDate('scan_time', Carbon::today('Asia/Jakarta'))
            ->orderBy('scan_time', 'desc')
            ->get();

        return view('admin.attendance.index', compact('attendances'));
    }

    public function scan(Request $request)
    {
        $request->validate([
            'serial_number' => 'required|string',
        ]);

        $serial = strtoupper(trim($request->serial_number));

        $card = NfcCard::with(['student.user'])->where('serial_number', $serial)->first();

        if (!$card) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu NFC tidak terdaftar.',
            ], 404);
        }

        $nowWib = Carbon::now('Asia/Jakarta');

        $alreadyScanned = Attendance::where('idstudent', $card->idstudent)
            ->whereDate('scan_time', Carbon::today('Asia/Jakarta'))
            ->exists();

        if ($alreadyScanned) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa sudah absen hari ini.',
                'student' => $card->student->user->nama_user ?? '-',
            ], 409);
        }

        $attendance = Attendance::create([
            'idstudent'  => $card->idstudent,
            'scan_time'  => $nowWib,
            'status'     => 'hadir',
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'Absensi berhasil dicatat.',
            'student'   => $card->student->user->nama_user ?? '-',
            'nim'       => $card->student->nim ?? '-',
            'scan_time' => $attendance->scan_time->setTimezone('Asia/Jakarta')->format('H:i:s'),
        ]);
    }
    
    // public function scan(Request $request)
    // {
    //     $request->validate([
    //         'serial_number' => 'required|string',
    //     ]);

    //     $serial = strtoupper(trim($request->serial_number));

    //     $card = NfcCard::with(['student.user'])->where('serial_number', $serial)->first();

    //     if (!$card) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Kartu NFC tidak terdaftar.',
    //         ], 404);
    //     }

    //     $nowWib = Carbon::now('Asia/Jakarta');

    //     $alreadyScanned = Attendance::where('idstudent', $card->idstudent)
    //         ->whereDate('scan_time', Carbon::today('Asia/Jakarta'))
    //         ->exists();

    //     if ($alreadyScanned) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Mahasiswa sudah absen hari ini.',
    //             'student' => $card->student->user->nama_user ?? '-',
    //         ], 409);
    //     }

    //     $attendance = Attendance::create([
    //         'idstudent'  => $card->idstudent,
    //         'scan_time'  => $nowWib,
    //         'status'     => 'hadir',
    //         'created_at' => $nowWib,
    //         'updated_at' => $nowWib,
    //     ]);

    //     return response()->json([
    //         'success'   => true,
    //         'message'   => 'Absensi berhasil dicatat.',
    //         'student'   => $card->student->user->nama_user ?? '-',
    //         'nim'       => $card->student->nim ?? '-',
    //         'scan_time' => $attendance->scan_time->setTimezone('Asia/Jakarta')->format('H:i:s'),
    //     ]);
    // }
}