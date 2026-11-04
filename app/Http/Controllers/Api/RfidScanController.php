<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Rfid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RfidScanController extends Controller
{
    public function scan(Request $request)
    {
        $validated = $request->validate([
            'uid' => ['required', 'string', 'max:100'],
        ]);

        $rfid = Rfid::with('siswa.kelas')
            ->where('uid', trim($validated['uid']))
            ->where('aktif', true)
            ->first();

        if (!$rfid || !$rfid->siswa || $rfid->siswa->status !== 'aktif') {
            return response()->json([
                'success' => false,
                'message' => 'Kartu RFID tidak terdaftar atau tidak aktif.',
            ], 404);
        }

        $siswa = $rfid->siswa;
        $sekarang = now();
        $tanggal = $sekarang->toDateString();

        $hasil = DB::transaction(function () use (
            $siswa,
            $sekarang,
            $tanggal
        ) {
            $absensi = Absensi::where('siswa_id', $siswa->id)
                ->whereDate('tanggal', $tanggal)
                ->lockForUpdate()
                ->first();

            if ($absensi) {
                return [
                    'status' => 'duplikat',
                    'absensi' => $absensi,
                ];
            }

            $jam = $sekarang->format('H:i:s');

            // Aturan jam ini adalah contoh sementara.
            if ($jam < '06:40:00') {
                return [
                    'status' => 'terlalu_awal',
                    'absensi' => null,
                ];
            }

            $status = $jam <= '07:30:00'
                ? 'hadir'
                : 'terlambat';

            $absensi = Absensi::create([
                'siswa_id' => $siswa->id,
                'tanggal' => $tanggal,
                'waktu_masuk' => $jam,
                'status' => $status,
            ]);

            return [
                'status' => 'tersimpan',
                'absensi' => $absensi,
            ];
        });

        if ($hasil['status'] === 'duplikat') {
            return response()->json([
                'success' => false,
                'message' => 'Absensi siswa hari ini sudah tercatat.',
                'siswa' => $siswa->nama_lengkap,
                'absensi' => $hasil['absensi'],
            ], 409);
        }

        if ($hasil['status'] === 'terlalu_awal') {
            return response()->json([
                'success' => false,
                'message' => 'Scan terlalu awal. Absensi dibuka pukul 06:40 WIB.',
                'siswa' => $siswa->nama_lengkap,
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil dicatat.',
            'siswa' => [
                'nama' => $siswa->nama_lengkap,
                'nisn' => $siswa->nisn,
                'kelas' => $siswa->kelas?->nama_kelas,
            ],
            'absensi' => $hasil['absensi'],
        ], 201);
    }
}
