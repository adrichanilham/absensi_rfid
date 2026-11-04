<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SiswaController extends Controller
{
    public function index()
    {
        $query = Siswa::with(['kelas', 'rfid']);
    
        if (auth()->user()->role === 'guru') {
            $query->whereHas('kelas', function ($q) {
                $q->where('wali_kelas_id', auth()->id());
            });
        }
    
        $siswa = $query
            ->orderBy('nama_lengkap')
            ->paginate(10);
    
        return view('siswa.index', compact('siswa'));
    }

    public function create()
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();

        return view('siswa.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nisn' => [
                'required',
                'string',
                'max:20',
                'unique:siswa,nisn',
            ],
            'nama_lengkap' => [
                'required',
                'string',
                'max:255',
            ],
            'jenis_kelamin' => [
                'required',
                Rule::in(['L', 'P']),
            ],
            'kelas_id' => [
                'required',
                'exists:kelas,id',
            ],
            'status' => [
                'required',
                Rule::in(['aktif', 'tidak_aktif']),
            ],
        ]);

        Siswa::create($validated);

        return redirect()
            ->route('siswa.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function edit(Siswa $siswa)
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();

        return view('siswa.edit', compact('siswa', 'kelas'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'nisn' => [
                'required',
                'string',
                'max:20',
                Rule::unique('siswa', 'nisn')->ignore($siswa->id),
            ],
            'nama_lengkap' => [
                'required',
                'string',
                'max:255',
            ],
            'jenis_kelamin' => [
                'required',
                Rule::in(['L', 'P']),
            ],
            'kelas_id' => [
                'required',
                'exists:kelas,id',
            ],
            'status' => [
                'required',
                Rule::in(['aktif', 'tidak_aktif']),
            ],
        ]);

        $siswa->update($validated);

        return redirect()
            ->route('siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa)
    {
        // Tidak menghapus permanen agar riwayat absensi tetap aman.
        $siswa->update(['status' => 'tidak_aktif']);

        return redirect()
            ->route('siswa.index')
            ->with('success', 'Siswa berhasil dinonaktifkan.');
    }
}
