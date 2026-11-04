
@extends('layouts.app')

@section('title', 'Edit Siswa')
@section('page-heading', 'Edit Data Siswa')

@section('content')
<style>
    .siswa-edit {
        max-width: 760px;
        margin: 0 auto;
        color: #334155;
    }

    .siswa-edit .form-panel {
        background: #fff;
        border: 1px solid #e8eaf0;
        border-radius: 14px;
        box-shadow: 0 4px 18px rgba(15, 23, 42, .04);
        overflow: hidden;
    }

    .siswa-edit .panel-header {
        padding: 20px 24px 17px;
        border-bottom: 1px solid #edf0f4;
    }

    .siswa-edit .panel-header h2 {
        margin: 0 0 6px;
        color: #850000;
        font: 600 23px Georgia, serif;
    }

    .siswa-edit .panel-header p {
        margin: 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.5;
    }

    .siswa-edit .form-body {
        padding: 22px 24px 24px;
    }

    .siswa-edit .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px 20px;
    }

    .siswa-edit .form-group {
        min-width: 0;
        margin: 0;
    }

    .siswa-edit .form-group.full {
        grid-column: 1 / -1;
    }

    .siswa-edit label {
        display: block;
        margin-bottom: 7px;
        color: #334155;
        font-size: 13px;
        font-weight: 600;
    }

    .siswa-edit input,
    .siswa-edit select {
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

    .siswa-edit input:focus,
    .siswa-edit select:focus {
        outline: none;
        border-color: #850000;
        box-shadow: 0 0 0 3px rgba(133, 0, 0, .09);
    }

    .siswa-edit .field-error {
        margin-top: 5px;
        color: #b91c1c;
        font-size: 12px;
    }

    .siswa-edit .validation-summary {
        margin-bottom: 18px;
        padding: 12px 15px;
        border: 1px solid #fecaca;
        border-radius: 9px;
        background: #fff7f7;
        color: #991b1b;
        font-size: 13px;
    }

    .siswa-edit .validation-summary p {
        margin: 0 0 6px;
        font-weight: 600;
    }

    .siswa-edit .validation-summary ul {
        margin: 0;
        padding-left: 20px;
    }

    .siswa-edit .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 24px;
        padding-top: 18px;
        border-top: 1px solid #edf0f4;
    }

    .siswa-edit .btn {
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

    .siswa-edit .btn-primary {
        background: #850000;
        color: #fff;
    }

    .siswa-edit .btn-primary:hover {
        background: #690000;
        transform: translateY(-1px);
    }

    .siswa-edit .btn-secondary {
        border-color: #d8dee8;
        background: #fff;
        color: #475569;
    }

    .siswa-edit .btn-secondary:hover {
        background: #f8fafc;
    }

    @media (max-width: 640px) {
        .siswa-edit .panel-header {
            padding: 17px 18px 15px;
        }

        .siswa-edit .form-body {
            padding: 18px;
        }

        .siswa-edit .form-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .siswa-edit .form-group.full {
            grid-column: auto;
        }

        .siswa-edit .form-actions {
            flex-direction: column-reverse;
        }

        .siswa-edit .btn {
            width: 100%;
        }
    }
</style>

<div class="siswa-edit">
    <section class="form-panel">
        <header class="panel-header">
            <h2>Edit Data Siswa</h2>
            <p>Perbarui identitas siswa di bawah ini.</p>
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

            <form action="{{ route('siswa.update', $siswa) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="form-group">
                        <label for="nisn">NISN</label>
                        <input
                            type="text"
                            id="nisn"
                            name="nisn"
                            value="{{ old('nisn', $siswa->nisn) }}"
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
                            value="{{ old('nama_lengkap', $siswa->nama_lengkap) }}"
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
                            <option value="L" @selected(old('jenis_kelamin', $siswa->jenis_kelamin) === 'L')>
                                Laki-laki
                            </option>
                            <option value="P" @selected(old('jenis_kelamin', $siswa->jenis_kelamin) === 'P')>
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
                                    @selected((string) old('kelas_id', $siswa->kelas_id) === (string) $item->id)
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
                            <option
                                value="aktif"
                                @selected(old('status', $siswa->status) === 'aktif')
                            >
                                Aktif
                            </option>
                            <option
                                value="tidak_aktif"
                                @selected(old('status', $siswa->status) === 'tidak_aktif')
                            >
                                Tidak Aktif
                            </option>
                        </select>
                        @error('status')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('siswa.index') }}" class="btn btn-secondary">
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
@endsection
