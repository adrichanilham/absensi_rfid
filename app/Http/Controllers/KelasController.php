<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KelasController extends Controller
{
    public function index()
    {
        $query = Kelas::with('waliKelas')
            ->withCount('siswa');
    
        if (auth()->user()->role === 'guru') {
            $query->where('wali_kelas_id', auth()->id());
        }
    
        $kelas = $query
            ->orderBy('nama_kelas')
            ->paginate(10);
    
        return view('kelas.index', compact('kelas'));
    }

    public function create()
    {
        $waliKelas = User::where('role', 'guru')
            ->orderBy('username')
            ->get();

        return view('kelas.create', compact('waliKelas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas' => [
                'required',
                'string',
                'max:100',
                'unique:kelas,nama_kelas',
            ],
            'wali_kelas_id' => [
                'nullable',
                'exists:users,id',
            ],
        ]);

        if (!empty($validated['wali_kelas_id'])) {
            $guru = User::findOrFail($validated['wali_kelas_id']);

            if ($guru->role !== 'guru') {
                return back()
                    ->withInput()
                    ->withErrors([
                        'wali_kelas_id' => 'Wali kelas harus merupakan akun guru.',
                    ]);
            }
        }

        Kelas::create($validated);

        return redirect()
            ->route('kelas.index')
            ->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function edit(Kelas $kela)
    {
        $waliKelas = User::where('role', 'guru')
            ->orderBy('username')
            ->get();

        return view('kelas.edit', [
            'kelas' => $kela,
            'waliKelas' => $waliKelas,
        ]);
    }

    public function update(Request $request, Kelas $kela)
    {
        $validated = $request->validate([
            'nama_kelas' => [
                'required',
                'string',
                'max:100',
                Rule::unique('kelas', 'nama_kelas')->ignore($kela->id),
            ],
            'wali_kelas_id' => [
                'nullable',
                'exists:users,id',
            ],
        ]);

        if (!empty($validated['wali_kelas_id'])) {
            $guru = User::findOrFail($validated['wali_kelas_id']);

            if ($guru->role !== 'guru') {
                return back()
                    ->withInput()
                    ->withErrors([
                        'wali_kelas_id' => 'Wali kelas harus merupakan akun guru.',
                    ]);
            }
        }

        $kela->update($validated);

        return redirect()
            ->route('kelas.index')
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kela)
    {
        if ($kela->siswa()->exists()) {
            return redirect()
                ->route('kelas.index')
                ->with('error', 'Kelas tidak dapat dihapus karena masih memiliki data siswa.');
        }

        $kela->delete();

        return redirect()
            ->route('kelas.index')
            ->with('success', 'Data kelas berhasil dihapus.');
    }
}
