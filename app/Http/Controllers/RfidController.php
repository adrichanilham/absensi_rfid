<?php

namespace App\Http\Controllers;

use App\Models\Rfid;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RfidController extends Controller
{
    public function index()
    {
        $rfid = Rfid::with('siswa')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('rfid.index', compact('rfid'));
    }

    public function create()
    {
        $siswa = Siswa::where('status', 'aktif')
            ->whereDoesntHave('rfid')
            ->orderBy('nama_lengkap')
            ->get();

        return view('rfid.create', compact('siswa'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'uid' => ['required', 'string', 'max:100', 'unique:rfid,uid'],
            'siswa_id' => [
                'required',
                'integer',
                Rule::exists('siswa', 'id')->where('status', 'aktif'),
            ],
        ]);

        if (Rfid::where('siswa_id', $validated['siswa_id'])->exists()) {
            return back()
                ->withInput()
                ->withErrors([
                    'siswa_id' => 'Siswa ini sudah memiliki kartu RFID.',
                ]);
        }

        Rfid::create([
            'uid' => trim($validated['uid']),
            'siswa_id' => $validated['siswa_id'],
            'aktif' => true,
            'terdaftar_pada' => now(),
        ]);

        return redirect()
            ->route('rfid.index')
            ->with('success', 'Kartu RFID berhasil didaftarkan.');
    }

    public function show(Rfid $rfid)
    {
        return redirect()->route('rfid.index');
    }

    public function edit(Rfid $rfid)
    {
        $siswa = Siswa::where('status', 'aktif')
            ->where(function ($query) use ($rfid) {
                $query->whereDoesntHave('rfid')
                    ->orWhere('id', $rfid->siswa_id);
            })
            ->orderBy('nama_lengkap')
            ->get();

        return view('rfid.edit', compact('rfid', 'siswa'));
    }

    public function update(Request $request, Rfid $rfid)
    {
        $validated = $request->validate([
            'uid' => [
                'required',
                'string',
                'max:100',
                Rule::unique('rfid', 'uid')->ignore($rfid->id),
            ],
            'siswa_id' => [
                'required',
                'integer',
                Rule::exists('siswa', 'id')->where('status', 'aktif'),
            ],
            'aktif' => ['required', Rule::in(['0', '1'])],
        ]);

        $kartuLain = Rfid::where('siswa_id', $validated['siswa_id'])
            ->where('id', '!=', $rfid->id)
            ->exists();

        if ($kartuLain) {
            return back()
                ->withInput()
                ->withErrors([
                    'siswa_id' => 'Siswa ini sudah memiliki kartu RFID lain.',
                ]);
        }

        $rfid->update([
            'uid' => trim($validated['uid']),
            'siswa_id' => $validated['siswa_id'],
            'aktif' => (bool) $validated['aktif'],
        ]);

        return redirect()
            ->route('rfid.index')
            ->with('success', 'Data kartu RFID berhasil diperbarui.');
    }

    public function destroy(Rfid $rfid)
    {
        // Nonaktifkan kartu, jangan langsung menghapus riwayatnya.
        $rfid->update(['aktif' => false]);

        return redirect()
            ->route('rfid.index')
            ->with('success', 'Kartu RFID berhasil dinonaktifkan.');
    }
}
