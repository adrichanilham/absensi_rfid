
@extends('layouts.app')

@section('title', 'Tambah Siswa')
@section('page-heading', 'Tambah Data Siswa')

@section('content')
<style>
    .siswa-create {
        max-width: 760px;
        margin: 0 auto;
        color: #334155;
    }

    .siswa-create .form-panel {
        background: #fff;
        border: 1px solid #e8eaf0;
        border-radius: 14px;
        box-shadow: 0 4px 18px rgba(15, 23, 42, .04);
        overflow: hidden;
    }

    .siswa-create .panel-header {
        padding: 20px 24px 17px;
        border-bottom: 1px solid #edf0f4;
    }

    .siswa-create .panel-header h2 {
        margin: 0 0 6px;
        color: #850000;
        font: 600 23px Georgia, serif;
    }

    .siswa-create .panel-header p {
        margin: 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.5;
    }

    .siswa-create .form-body {
        padding: 22px 24px 24px;
    }

    .siswa-create .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px 20px;
    }

    .siswa-create .form-group {
        min-width: 0;
        margin: 0;
    }

    .siswa-create .form-group.full {
        grid-column: 1 / -1;
    }

    .siswa-create label {
        display: block;
        margin-bottom: 7px;
        color: #334155;
        font-size: 13px;
        font-weight: 600;
    }

    .siswa-create input,
    .siswa-create select {
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

    .siswa-create input:focus,
    .siswa-create select:focus {
        outline: none;
        border-color: #850000;
        box-shadow: 0 0 0 3px rgba(133, 0, 0, .09);
    }

    .siswa-create .field-error {
        margin-top: 5px;
        color: #b91c1c;
        font-size: 12px;
    }

    .siswa-create .validation-summary {
        margin-bottom: 18px;
        padding: 12px 15px;
        border: 1px solid #fecaca;
        border-radius: 9px;
        background: #fff7f7;
        color: #991b1b;
        font-size: 13px;
    }

    .siswa-create .validation-summary p {
        margin: 0 0 6px;
        font-weight: 600;
    }

    .siswa-create .validation-summary ul {
        margin: 0;
        padding-left: 20px;
    }

    .siswa-create .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 24px;
        padding-top: 18px;
        border-top: 1px solid #edf0f4;
    }

    .siswa-create .btn {
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

    .siswa-create .btn-primary {
        background: #850000;
        color: #fff;
    }

    .siswa-create .btn-primary:hover {
        background: #690000;
        transform: translateY(-1px);
    }

    .siswa-create .btn-secondary {
        border-color: #d8dee8;
        background: #fff;
        color: #475569;
    }

    .siswa-create .btn-secondary:hover {
        background: #f8fafc;
    }

    @media (max-width: 640px) {
        .siswa-create .panel-header {
            padding: 17px 18px 15px;
        }

        .siswa-create .form-body {
            padding: 18px;
        }

        .siswa-create .form-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .siswa-create .form-group.full {
            grid-column: auto;
        }

        .siswa-create .form-actions {
            flex-direction: column-reverse;
        }

        .siswa-create .btn {
            width: 100%;
        }
    }
</style>

<div class="siswa-create">
    <section class="form-panel">
        <header class="panel-header">
            <h2>Tambah Data Siswa</h2>
            <p>Masukkan identitas siswa dengan benar.</p>
        </header>

        <div class="form-body">
            @if ($errors->any())
                <div class="validation-summary">
                    <p>Periksa kembali data berikut:</p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('siswa.store') }}" method="POST">
                @csrf

                <div class="form-grid">
                    <div class="form-group">
                        <label for="nisn">NISN</label>
                        <input
                            type="text"
                            id="nisn"
                            name="nisn"
                            value="{{ old('nisn') }}"
                            maxlength="20"
                            required
                        >
                        @error('nisn')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="nama_lengkap">Nama Lengkap</label>
                        <input
                            type="text"
                            id="nama_lengkap"
                            name="nama_lengkap"
                            value="{{ old('nama_lengkap') }}"
                            maxlength="255"
                            required
                        >
                        @error('nama_lengkap')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="jenis_kelamin">Jenis Kelamin</label>
                        <select id="jenis_kelamin" name="jenis_kelamin" required>
                            <option value="">-- Pilih jenis kelamin --</option>
                            <option value="L" @selected(old('jenis_kelamin') === 'L')>
                                Laki-laki
                            </option>
                            <option value="P" @selected(old('jenis_kelamin') === 'P')>
                                Perempuan
                            </option>
                        </select>
                        @error('jenis_kelamin')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="kelas_id">Kelas</label>
                        <select id="kelas_id" name="kelas_id" required>
                            <option value="">-- Pilih kelas --</option>
                            @foreach ($kelas as $item)
                                <option
                                    value="{{ $item->id }}"
                                    @selected((string) old('kelas_id') === (string) $item->id)
                                >
                                    {{ $item->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                        @error('kelas_id')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group full">
                        <label for="status">Status Siswa</label>
                        <select id="status" name="status" required>
                            <option value="aktif" @selected(old('status', 'aktif') === 'aktif')>
                                Aktif
                            </option>
                            <option value="tidak_aktif" @selected(old('status') === 'tidak_aktif')>
                                Tidak Aktif
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('siswa.index') }}" class="btn btn-secondary">
                        Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Simpan Siswa
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
