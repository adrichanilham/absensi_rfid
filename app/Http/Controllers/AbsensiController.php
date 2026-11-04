<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => ['nullable', 'date'],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
            'status' => [
                'nullable',
                Rule::in([
                    'hadir',
                    'terlambat',
                    'sakit',
                    'izin',
                    'alpa',
                ]),
            ],
        ]);

        $query = Absensi::with(['siswa.kelas']);

        if (!empty($validated['tanggal'])) {
            $query->whereDate('tanggal', $validated['tanggal']);
        }

        if (!empty($validated['kelas_id'])) {
            $query->whereHas('siswa', function ($q) use ($validated) {
                $q->where('kelas_id', $validated['kelas_id']);
            });
        }

        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        // Guru hanya boleh melihat absensi kelas yang diampunya.
        if (auth()->user()->role === 'guru') {
            $query->whereHas('siswa.kelas', function ($q) {
                $q->where('wali_kelas_id', auth()->id());
            });
        }

        $absensi = $query
            ->orderByDesc('tanggal')
            ->orderByDesc('waktu_masuk')
            ->paginate(15)
            ->withQueryString();

        $kelasQuery = Kelas::query()->orderBy('nama_kelas');

        if (auth()->user()->role === 'guru') {
            $kelasQuery->where('wali_kelas_id', auth()->id());
        }

        $kelas = $kelasQuery->get();

        return view('absensi.index', [
            'absensi' => $absensi,
            'kelas' => $kelas,
            'filters' => $validated,
        ]);
    }

    public function edit(Absensi $absensi)
    {
        abort_unless(
            auth()->user()->role === 'operator',
            403
        );

        $absensi->load('siswa.kelas');

        return view('absensi.edit', compact('absensi'));
    }

    public function update(Request $request, Absensi $absensi)
    {
        abort_unless(
            auth()->user()->role === 'operator',
            403
        );

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'hadir',
                    'terlambat',
                    'sakit',
                    'izin',
                    'alpa',
                ]),
            ],
            'waktu_masuk' => ['nullable', 'date_format:H:i'],
            'waktu_keluar' => ['nullable', 'date_format:H:i'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ]);

        $absensi->update([
            'status' => $validated['status'],
            'waktu_masuk' => !empty($validated['waktu_masuk'])
                ? $validated['waktu_masuk'] . ':00'
                : null,
            'waktu_keluar' => !empty($validated['waktu_keluar'])
                ? $validated['waktu_keluar'] . ':00'
                : null,
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return redirect()
            ->route('absensi.index')
            ->with('success', 'Data absensi berhasil diperbarui.');
    }
}
