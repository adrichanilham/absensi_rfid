<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->with(['anak', 'kelasDiampu'])
            ->orderBy('username')
            ->paginate(10);

        return view('operator.users.index', compact('users'));
    }

    public function create()
    {
        $siswas = Siswa::whereNull('wali_murid_id')
            ->orderBy('nama_lengkap')
            ->get();

        return view('operator.users.create', compact('siswas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => [
                'required', 'string', 'max:50', 'unique:users,username',
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['operator', 'guru', 'wali_murid'])],
            'siswa_id' => ['nullable', 'required_if:role,wali_murid', 'exists:siswa,id'],
        ]);

        DB::transaction(function () use ($data) {
            $user = User::create([
                'username' => $data['username'],
                'password' => $data['password'],
                'role' => $data['role'],
            ]);

            if ($user->role === 'wali_murid') {
                $siswa = Siswa::whereKey($data['siswa_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($siswa->wali_murid_id !== null) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'siswa_id' => 'Siswa tersebut sudah memiliki akun wali murid.',
                    ]);
                }

                $siswa->update(['wali_murid_id' => $user->id]);
            }
        });

        return redirect()->route('operator.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $siswas = Siswa::where(function ($query) use ($user) {
            $query->whereNull('wali_murid_id');

            if ($user->role === 'wali_murid') {
                $query->orWhere('wali_murid_id', $user->id);
            }
        })->orderBy('nama_lengkap')->get();

        return view('operator.users.edit', compact('user', 'siswas'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->id === auth()->id() && $request->role !== 'operator') {
            return back()->withErrors([
                'role' => 'Kamu tidak bisa mengubah role akun operator yang sedang digunakan.',
            ])->withInput();
        }

        $data = $request->validate([
            'username' => [
                'required', 'string', 'max:50',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'role' => ['required', Rule::in(['operator', 'guru', 'wali_murid'])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'siswa_id' => [
                'nullable',
                'required_if:role,wali_murid',
                'exists:siswa,id',
            ],
        ]);

        DB::transaction(function () use ($data, $user) {
            $user = User::whereKey($user->id)->lockForUpdate()->firstOrFail();

            $roleLama = $user->role;
            $userId = $user->id;

            $user->username = $data['username'];
            $user->role = $data['role'];

            if (!empty($data['password'])) {
                $user->password = $data['password'];
            }

            $user->save();

            if ($roleLama === 'wali_murid' && $data['role'] !== 'wali_murid') {
                Siswa::where('wali_murid_id', $userId)
                    ->update(['wali_murid_id' => null]);
            }

            if ($data['role'] === 'wali_murid') {
                $siswa = Siswa::whereKey($data['siswa_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    $siswa->wali_murid_id !== null
                    && $siswa->wali_murid_id !== $userId
                ) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'siswa_id' => 'Siswa tersebut sudah terhubung ke akun wali murid lain.',
                    ]);
                }

                Siswa::where('wali_murid_id', $userId)
                    ->where('id', '!=', $siswa->id)
                    ->update(['wali_murid_id' => null]);

                $siswa->update(['wali_murid_id' => $userId]);
            }
        });

        return redirect()->route('operator.users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Kamu tidak bisa menghapus akun yang sedang digunakan.');
        }

        if (
            $user->role === 'operator'
            && User::where('role', 'operator')->count() <= 1
        ) {
            return back()->with('error', 'Operator terakhir tidak boleh dihapus.');
        }

        if ($user->kelasDiampu()->exists()) {
            return back()->with('error', 'Akun guru masih menjadi wali kelas. Pindahkan wali kelas terlebih dahulu.');
        }

        if ($user->pengajuanIzinDiverifikasi()->exists()) {
            return back()->with('error', 'Akun ini tercatat sebagai verifikator pengajuan izin dan tidak bisa dihapus.');
        }

        if ($user->anak()->exists()) {
            return back()->with('error', 'Akun wali murid masih terhubung dengan siswa. Ubah hubungan tersebut terlebih dahulu.');
        }

        $user->delete();

        return redirect()->route('operator.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
