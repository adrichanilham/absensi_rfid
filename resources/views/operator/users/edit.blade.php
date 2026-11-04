
@extends('layouts.app')

@section('title', 'Edit Pengguna')
@section('page-heading', 'Edit Pengguna')

@section('content')
<style>
    .user-edit {
        max-width: 760px;
        margin: 0 auto;
        color: #334155;
    }

    .user-edit .form-panel {
        background: #fff;
        border: 1px solid #e8eaf0;
        border-radius: 14px;
        box-shadow: 0 4px 18px rgba(15, 23, 42, .04);
        overflow: hidden;
    }

    .user-edit .panel-header {
        padding: 20px 24px 17px;
        border-bottom: 1px solid #edf0f4;
    }

    .user-edit .panel-header h2 {
        margin: 0 0 6px;
        color: #850000;
        font: 600 23px Georgia, serif;
    }

    .user-edit .panel-header p {
        margin: 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.5;
    }

    .user-edit .username-value {
        color: #334155;
        font-weight: 700;
    }

    .user-edit .form-body {
        padding: 22px 24px 24px;
    }

    .user-edit .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 17px 20px;
    }

    .user-edit .form-group {
        min-width: 0;
    }

    .user-edit .form-group.full {
        grid-column: 1 / -1;
    }

    .user-edit label {
        display: block;
        margin-bottom: 7px;
        color: #334155;
        font-size: 13px;
        font-weight: 600;
    }

    .user-edit input,
    .user-edit select {
        width: 100%;
        min-height: 42px;
        padding: 10px 12px;
        border: 1px solid #d8dee8;
        border-radius: 8px;
        background: #fff;
        color: #1e293b;
        font: inherit;
        font-size: 14px;
        transition: border-color .18s ease, box-shadow .18s ease;
    }

    .user-edit input:focus,
    .user-edit select:focus {
        outline: none;
        border-color: #850000;
        box-shadow: 0 0 0 3px rgba(133, 0, 0, .09);
    }

    .user-edit .field-error {
        margin-top: 5px;
        color: #b91c1c;
        font-size: 12px;
    }

    .user-edit .validation-summary {
        margin-bottom: 18px;
        padding: 12px 15px;
        border: 1px solid #fecaca;
        border-radius: 9px;
        background: #fff7f7;
        color: #991b1b;
        font-size: 13px;
    }

    .user-edit .validation-summary ul {
        margin: 0;
        padding-left: 20px;
    }

    .user-edit .hint {
        margin: 7px 0 0;
        color: #64748b;
        font-size: 12px;
        line-height: 1.5;
    }

    .user-edit .linked-student {
        padding: 15px;
        border: 1px solid #f0dada;
        border-radius: 10px;
        background: #fffafa;
    }

    .user-edit .linked-student[hidden] {
        display: none !important;
    }

    .user-edit .linked-student-title {
        margin: 0 0 12px;
        color: #850000;
        font-size: 13px;
        font-weight: 700;
    }

    .user-edit .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 24px;
        padding-top: 18px;
        border-top: 1px solid #edf0f4;
    }

    .user-edit .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        padding: 9px 16px;
        border: 1px solid transparent;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: background .18s ease, transform .18s ease;
    }

    .user-edit .btn-primary {
        background: #850000;
        color: #fff;
    }

    .user-edit .btn-primary:hover {
        background: #690000;
        transform: translateY(-1px);
    }

    .user-edit .btn-secondary {
        border-color: #d8dee8;
        background: #fff;
        color: #475569;
    }

    .user-edit .btn-secondary:hover {
        background: #f8fafc;
    }

    @media (max-width: 640px) {
        .user-edit .panel-header {
            padding: 17px 18px 15px;
        }

        .user-edit .form-body {
            padding: 18px;
        }

        .user-edit .form-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .user-edit .form-group.full {
            grid-column: auto;
        }

        .user-edit .form-actions {
            flex-direction: column-reverse;
        }

        .user-edit .btn {
            width: 100%;
        }
    }
</style>

<div class="user-edit">
    <section class="form-panel">
        <header class="panel-header">
            <h2>Edit Pengguna</h2>
            <p>Username: <span class="username-value">{{ $user->username }}</span></p>
        </header>

        <div class="form-body">
            @if ($errors->any())
                <div class="validation-summary">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('operator.users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input
                            id="username"
                            name="username"
                            value="{{ old('username', $user->username) }}"
                            required
                            maxlength="50"
                            autocomplete="username"
                        >
                        @error('username')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="role">Role pengguna</label>
                        <select id="role" name="role" required>
                            <option value="operator" @selected(old('role', $user->role) === 'operator')>Operator</option>
                            <option value="guru" @selected(old('role', $user->role) === 'guru')>Guru</option>
                            <option value="wali_murid" @selected(old('role', $user->role) === 'wali_murid')>Wali Murid</option>
                        </select>
                        @error('role')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group full">
                        <div id="pilihan-siswa" class="linked-student" hidden>
                            <p class="linked-student-title">Hubungkan akun dengan siswa</p>

                            <label for="siswa_id">Anak yang terhubung</label>
                            <select id="siswa_id" name="siswa_id">
                                <option value="">-- Pilih siswa --</option>
                                @foreach ($siswas as $siswa)
                                    <option
                                        value="{{ $siswa->id }}"
                                        @selected((string) old('siswa_id', $user->anak?->id) === (string) $siswa->id)
                                    >
                                        {{ $siswa->nama_lengkap }} — {{ $siswa->NISN ?? $siswa->nisn ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('siswa_id')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password baru (opsional)</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            minlength="8"
                            autocomplete="new-password"
                        >
                        <p class="hint">Kosongkan jika tidak ingin mengganti password.</p>
                        @error('password')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi password baru</label>
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            minlength="8"
                            autocomplete="new-password"
                        >
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('operator.users.index') }}" class="btn btn-secondary">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>

<script>
    const roleSelect = document.getElementById('role');
    const pilihanSiswa = document.getElementById('pilihan-siswa');
    const siswaSelect = document.getElementById('siswa_id');

    function aturPilihanSiswa() {
        const waliMurid = roleSelect.value === 'wali_murid';
        pilihanSiswa.hidden = !waliMurid;
        siswaSelect.required = waliMurid;
    }

    roleSelect.addEventListener('change', aturPilihanSiswa);
    aturPilihanSiswa();
</script>
@endsection
