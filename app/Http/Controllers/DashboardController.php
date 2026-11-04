<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Siswa;

class DashboardController extends Controller
{
    public function index()
    {
        $hariIni = today();

        // Jumlah siswa yang masih aktif
        $totalSiswa = Siswa::where('status', 'aktif')->count();

        // Jumlah absensi berdasarkan status hari ini
        $hadirHariIni = Absensi::whereDate('tanggal', $hariIni)
            ->where('status', 'hadir')
            ->count();

        $terlambatHariIni = Absensi::whereDate('tanggal', $hariIni)
            ->where('status', 'terlambat')
            ->count();

        // Hitung siswa aktif yang sudah memiliki catatan hari ini
        $siswaSudahAdaCatatan = Absensi::whereDate('tanggal', $hariIni)
            ->whereIn('status', [
                'hadir',
                'terlambat',
                'sakit',
                'izin',
                'alpa',
            ])
            ->distinct()
            ->count('siswa_id');

        // Belum ada catatan bukan berarti otomatis alpa
        $belumAdaCatatan = max(
            0,
            $totalSiswa - $siswaSudahAdaCatatan
        );

        // Ambil 10 catatan absensi terbaru hari ini
        $absensiTerbaru = Absensi::with('siswa.kelas')
            ->whereDate('tanggal', $hariIni)
            ->orderByDesc('waktu_masuk')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('dashboard.index', compact(
            'hariIni',
            'totalSiswa',
            'hadirHariIni',
            'terlambatHariIni',
            'belumAdaCatatan',
            'absensiTerbaru'
        ));
    }
}
