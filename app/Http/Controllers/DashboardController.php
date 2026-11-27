<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Siswa;

class DashboardController extends Controller
{
    public function index()
    {
        $hariIni = today();
        $user = auth()->user();
        $isGuru = $user && $user->role === 'guru';

        $kelasGuruIds = collect();
        $namaKelasGuru = null;

        if ($isGuru) {
            $kelasDiampu = Kelas::where('wali_kelas_id', $user->id)->get();
            $kelasGuruIds = $kelasDiampu->pluck('id');
            $namaKelasGuru = $kelasDiampu->pluck('nama_kelas')->implode(', ') ?: null;
        }

        // Jumlah siswa aktif (khusus kelas yang diampu jika role guru)
        $siswaQuery = Siswa::where('status', 'aktif');
        if ($isGuru) {
            $siswaQuery->whereIn('kelas_id', $kelasGuruIds);
        }
        $totalSiswa = $siswaQuery->count();

        // Helper query absensi hari ini
        $absensiHariIniQuery = function () use ($hariIni, $isGuru, $kelasGuruIds) {
            return Absensi::whereDate('tanggal', $hariIni)
                ->when($isGuru, function ($query) use ($kelasGuruIds) {
                    $query->whereHas('siswa', function ($siswaQ) use ($kelasGuruIds) {
                        $siswaQ->whereIn('kelas_id', $kelasGuruIds);
                    });
                });
        };

        $hadirHariIni = (clone $absensiHariIniQuery())
            ->where('status', 'hadir')
            ->count();

        $terlambatHariIni = (clone $absensiHariIniQuery())
            ->where('status', 'terlambat')
            ->count();

        // Hitung siswa aktif yang sudah memiliki catatan hari ini
        $siswaSudahAdaCatatan = (clone $absensiHariIniQuery())
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
        $absensiTerbaru = (clone $absensiHariIniQuery())
            ->with('siswa.kelas')
            ->orderByDesc('waktu_masuk')
            ->orderByDesc('id')
            ->get();

        return view('dashboard.index', compact(
            'hariIni',
            'totalSiswa',
            'hadirHariIni',
            'terlambatHariIni',
            'belumAdaCatatan',
            'absensiTerbaru',
            'namaKelasGuru'
        ));
    }
}
