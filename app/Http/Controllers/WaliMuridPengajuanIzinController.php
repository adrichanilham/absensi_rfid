<?php

namespace App\Http\Controllers;

use App\Models\PengajuanIzin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WaliMuridPengajuanIzinController extends Controller
{
    public function index()
    {
        $anak = auth()->user()->anak()->firstOrFail();

        $pengajuan = PengajuanIzin::where('siswa_id', $anak->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view(
            'wali-murid.pengajuan.index',
            compact('anak', 'pengajuan')
        );
    }

    public function create()
    {
        $anak = auth()->user()->anak()->firstOrFail();

        return view('wali-murid.pengajuan.create', compact('anak'));
    }

    public function store(Request $request)
    {
        $anak = auth()->user()->anak()->firstOrFail();

        $data = $request->validate([
            'jenis' => ['required', 'in:izin,sakit'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => [
                'required',
                'date',
                'after_or_equal:tanggal_mulai',
            ],
            'alasan' => ['required', 'string', 'max:1000'],
            'dokumen' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048',
            ],
        ]);

        $dokumenPath = null;

        if ($request->hasFile('dokumen')) {
            $dokumenPath = $request->file('dokumen')
                ->store('dokumen-pengajuan', 'local');
        }

        PengajuanIzin::create([
            'siswa_id' => $anak->id,
            'tanggal_mulai' => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai'],
            'jenis' => $data['jenis'],
            'alasan' => $data['alasan'],
            'dokumen_path' => $dokumenPath,
            'status' => 'menunggu',
        ]);

        return redirect()
            ->route('wali.pengajuan.index')
            ->with('success', 'Pengajuan berhasil dikirim dan menunggu verifikasi Operator.');
    }
}
