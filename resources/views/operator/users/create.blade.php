
@extends('layouts.app')

@section('title', 'Tambah Pengguna')
@section('page-heading', 'Tambah Pengguna')

@section('content')
<style>
    .user-create {
        max-width: 760px;
        margin: 0 auto;
        color: #334155;
    }

    .user-create .form-panel {
        background: #fff;
        border: 1px solid #e8eaf0;
        border-radius: 14px;
        box-shadow: 0 4px 18px rgba(15, 23, 42, .04);
        overflow: hidden;
    }

    .user-create .panel-header {
        padding: 20px 24px 17px;
        border-bottom: 1px solid #edf0f4;
    }

    .user-create .panel-header h2 {
        margin: 0 0 6px;
        color: #850000;
        font: 600 23px Georgia, serif;
    }

    .user-create .panel-header p {
        margin: 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.5;
    }

    .user-create .form-body {
        padding: 22px 24px 24px;
    }

    .user-create .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 17px 20px;
    }

    .user-create .form-group {
        min-width: 0;
    }

    .user-create .form-group.full {
        grid-column: 1 / -1;
    }

    .user-create label {
        display: block;
        margin-bottom: 7px;
        color: #334155;
        font-size: 13px;
        font-weight: 600;
    }

    .user-create input,
    .user-create select {
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

    .user-create input:focus,
    .user-create select:focus {
        outline: none;
        border-color: #850000;
        box-shadow: 0 0 0 3px rgba(133, 0, 0, .09);
    }

    .user-create .field-error {
        margin-top: 5px;
        color: #b91c1c;
        font-size: 12px;
    }

    .user-create .validation-summary {
        margin-bottom: 18px;
        padding: 12px 15px;
        border: 1px solid #fecaca;
        border-radius: 9px;
        background: #fff7f7;
        color: #991b1b;
        font-size: 13px;
    }

    .user-create .validation-summary ul {
        margin: 0;
        padding-left: 20px;
    }

    .user-create .hint {
        margin: 7px 0 0;
        color: #64748b;
        font-size: 12px;
        line-height: 1.5;
    }

    .user-create .linked-student {
        margin-top: 2px;
        padding: 15px;
        border: 1px solid #f0dada;
        border-radius: 10px;
        background: #fffafa;
    }

    .user-create .linked-student[hidden] {
        display: none !important;
    }

    .user-create .linked-student-title {
        margin: 0 0 12px;
        color: #850000;
        font-size: 13px;
        font-weight: 700;
    }

    .user-create .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 24px;
        padding-top: 18px;
        border-top: 1px solid #edf0f4;
    }

    .user-create .btn {
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

    .user-create .btn-primary {
        background: #850000;
        color: #fff;
    }

    .user-create .btn-primary:hover {
        background: #690000;
        transform: translateY(-1px);
    }

    .user-create .btn-secondary {
        border-color: #d8dee8;
        background: #fff;
        color: #475569;
    }

    .user-create .btn-secondary:hover {
        background: #f8fafc;
    }

    @media (max-width: 640px) {
        .user-create .panel-header {
            padding: 17px 18px 15px;
        }

        .user-create .form-body {
            padding: 18px;
        }

        .user-create .form-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .user-create .form-group.full {
            grid-column: auto;
        }

        .user-create .form-actions {
            flex-direction: column-reverse;
        }

        .user-create .btn {
            width: 100%;
        }
    }
</style>

<div class="user-create">
    <section class="form-panel">
        <header class="panel-header">
            <h2>Tambah Pengguna</h2>
            <p>Buat akun baru untuk operator, guru, atau wali murid.</p>
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

            <form method="POST" action="{{ route('operator.users.store') }}">
                @csrf

                <div class="form-grid">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input
                            id="username"
                            name="username"
                            value="{{ old('username') }}"
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
                            <option value="">-- Pilih role --</option>
                            <option value="operator" @selected(old('role') === 'operator')>Operator</option>
                            <option value="guru" @selected(old('role') === 'guru')>Guru</option>
                            <option value="wali_murid" @selected(old('role') === 'wali_murid')>Wali Murid</option>
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
                                        @selected((string) old('siswa_id') === (string) $siswa->id)
                                    >
                                        {{ $siswa->nama_lengkap }} — {{ $siswa->NISN ?? $siswa->nisn ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('siswa_id')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                            <p class="hint">Hanya siswa yang belum memiliki akun wali murid yang ditampilkan.</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            minlength="8"
                            autocomplete="new-password"
                        >
                        @error('password')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi password</label>
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            required
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
                        Simpan Pengguna
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
