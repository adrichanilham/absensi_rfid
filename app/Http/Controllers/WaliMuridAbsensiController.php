<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;

class WaliMuridAbsensiController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => [
                'nullable',
                'date',
                'after_or_equal:tanggal_mulai',
            ],
        ]);

        $wali = auth()->user();
        $anak = $wali->anak()->firstOrFail();

        $query = Absensi::where('siswa_id', $anak->id);

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate(
                'tanggal',
                '>=',
                $request->tanggal_mulai
            );
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate(
                'tanggal',
                '<=',
                $request->tanggal_selesai
            );
        }

        $absensi = $query
            ->orderByDesc('tanggal')
            ->paginate(10)
            ->withQueryString();

        return view(
            'wali-murid.absensi.index',
            compact('anak', 'absensi')
        );
    }
}
