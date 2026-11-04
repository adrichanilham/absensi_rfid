<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;

class LaporanAbsensiController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => [
                'nullable',
                'date',
                'after_or_equal:tanggal_mulai',
            ],
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
        ]);

        $tanggalMulai = $validated['tanggal_mulai']
            ?? now()->startOfMonth()->toDateString();

        $tanggalSelesai = $validated['tanggal_selesai']
            ?? now()->toDateString();

        $kelasId = $validated['kelas_id'] ?? null;

        // Daftar kelas yang boleh dilihat pengguna.
        $kelasQuery = Kelas::query()->orderBy('nama_kelas');

        if (auth()->user()->role === 'guru') {
            $kelasQuery->where('wali_kelas_id', auth()->id());
        }

        $kelas = $kelasQuery->get();

        // Ambil siswa aktif dan catatan absensinya dalam periode laporan.
        $siswaQuery = Siswa::query()
            ->where('status', 'aktif')
            ->with([
                'kelas',
                'absensi' => function ($query) use (
                    $tanggalMulai,
                    $tanggalSelesai
                ) {
                    $query->whereBetween('tanggal', [
                        $tanggalMulai,
                        $tanggalSelesai,
                    ])->orderBy('tanggal');
                },
            ]);

        if ($kelasId !== null) {
            $siswaQuery->where('kelas_id', $kelasId);
        }

        // Batasi guru pada kelas yang diampunya.
        if (auth()->user()->role === 'guru') {
            $siswaQuery->whereHas('kelas', function ($query) {
                $query->where('wali_kelas_id', auth()->id());
            });
        }

        $siswa = $siswaQuery
            ->orderBy('nama_lengkap')
            ->get();

        // Hitung hanya catatan yang benar-benar tersimpan.
        $semuaAbsensi = $siswa->flatMap(
            fn ($item) => $item->absensi
        );

        $rekap = [
            'hadir' => $semuaAbsensi->where('status', 'hadir')->count(),
            'terlambat' => $semuaAbsensi->where('status', 'terlambat')->count(),
            'sakit' => $semuaAbsensi->where('status', 'sakit')->count(),
            'izin' => $semuaAbsensi->where('status', 'izin')->count(),
            'alpa' => $semuaAbsensi->where('status', 'alpa')->count(),
            'total_catatan' => $semuaAbsensi->count(),
        ];

        return view('laporan.absensi', [
            'siswa' => $siswa,
            'kelas' => $kelas,
            'rekap' => $rekap,
            'tanggalMulai' => $tanggalMulai,
            'tanggalSelesai' => $tanggalSelesai,
            'kelasId' => $kelasId,
        ]);
    }
}
