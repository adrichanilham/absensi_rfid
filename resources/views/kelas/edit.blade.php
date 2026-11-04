
@extends('layouts.app')

@section('title', 'Edit Kelas')
@section('page-heading', 'Edit Data Kelas')

@section('content')
<style>
    .class-form-page {
        max-width: 760px;
        margin: 0 auto;
        color: #292929;
        font-family: Arial, sans-serif;
        font-size: 13px;
    }

    .class-form-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 16px;
        color: #850000;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
    }

    .class-form-back:hover {
        color: #650000;
        text-decoration: underline;
    }

    .class-form-heading {
        margin-bottom: 18px;
    }

    .class-form-heading h2 {
        margin: 0 0 5px;
        color: #850000;
        font-family: Georgia, serif;
        font-size: 21px;
    }

    .class-form-heading p {
        margin: 0;
        color: #777;
        font-size: 12px;
        line-height: 1.5;
    }

    .class-form-card {
        overflow: hidden;
        border: 1px solid #e8e1e1;
        border-radius: 9px;
        background: #fff;
    }

    .class-form-card-header {
        padding: 13px 16px;
        background: #850000;
        color: #fff;
    }

    .class-form-card-header h3 {
        margin: 0 0 4px;
        font-family: Georgia, serif;
        font-size: 15px;
    }

    .class-form-card-header p {
        margin: 0;
        color: #f4dddd;
        font-size: 11px;
    }

    .class-form-card-body {
        padding: 18px;
    }

    .class-form-group {
        margin-bottom: 17px;
    }

    .class-form-group:last-of-type {
        margin-bottom: 0;
    }

    .class-form-group label {
        display: block;
        margin-bottom: 6px;
        color: #555;
        font-size: 12px;
        font-weight: 600;
    }

    .class-form-control {
        display: block;
        width: 100%;
        min-height: 38px;
        padding: 9px 11px;
        border: 1px solid #ddd;
        border-radius: 6px;
        background: #fff;
        color: #333;
        font: inherit;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }

    .class-form-control:focus {
        border-color: #850000;
        box-shadow: 0 0 0 3px rgba(133, 0, 0, .08);
    }

    .class-form-help {
        display: block;
        margin-top: 6px;
        color: #888;
        font-size: 11px;
        line-height: 1.5;
    }

    .class-form-error {
        margin-top: 5px;
        color: #b91c1c;
        font-size: 11px;
    }

    .class-form-errors {
        margin-bottom: 15px;
        padding: 11px 14px;
        border: 1px solid #f1caca;
        border-radius: 7px;
        background: #fff5f5;
        color: #b91c1c;
        font-size: 12px;
    }

    .class-form-errors ul {
        margin: 6px 0 0;
        padding-left: 18px;
    }

    .class-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 22px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }

    .class-form-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 36px;
        padding: 0 15px;
        border: 1px solid #850000;
        border-radius: 6px;
        background: #850000;
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: background .2s;
    }

    .class-form-btn:hover {
        background: #690000;
        color: #fff;
    }

    .class-form-btn.secondary {
        border-color: #ddd;
        background: #fff;
        color: #555;
    }

    .class-form-btn.secondary:hover {
        background: #f7f7f7;
    }

    @media (max-width: 550px) {
        .class-form-card-body {
            padding: 13px;
        }

        .class-form-actions {
            flex-direction: column-reverse;
        }

        .class-form-btn {
            width: 100%;
        }
    }
</style>

<div class="class-form-page">

    <a href="{{ route('kelas.index') }}" class="class-form-back">
        ← Kembali ke data kelas
    </a>

    <div class="class-form-heading">
        <h2>Edit Data Kelas</h2>
        <p>Perbarui nama kelas atau wali kelas.</p>
    </div>

    @if ($errors->any())
        <div class="class-form-errors">
            <strong>Periksa kembali data berikut:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="class-form-card">
        <div class="class-form-card-header">
            <h3>Informasi Kelas</h3>
            <p>Perbarui informasi kelas pada formulir berikut</p>
        </div>

        <div class="class-form-card-body">
            <form action="{{ route('kelas.update', $kelas) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="class-form-group">
                    <label for="nama_kelas">Nama Kelas</label>
                    <input
                        type="text"
                        id="nama_kelas"
                        name="nama_kelas"
                        class="class-form-control"
                        value="{{ old('nama_kelas', $kelas->nama_kelas) }}"
                        maxlength="100"
                        required
                    >

                    @error('nama_kelas')
                        <div class="class-form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="class-form-group">
                    <label for="wali_kelas_id">Wali Kelas (Opsional)</label>
                    <select
                        id="wali_kelas_id"
                        name="wali_kelas_id"
                        class="class-form-control"
                    >
                        <option value="">-- Belum ditentukan --</option>

                        @foreach ($waliKelas as $guru)
                            <option
                                value="{{ $guru->id }}"
                                @selected((string) old('wali_kelas_id', $kelas->wali_kelas_id) === (string) $guru->id)
                            >
                                {{ $guru->username }}
                            </option>
                        @endforeach
                    </select>

                    @error('wali_kelas_id')
                        <div class="class-form-error">{{ $message }}</div>
                    @enderror

                    @if ($waliKelas->isEmpty())
                        <small class="class-form-help">
                            Belum ada akun dengan role guru. Kamu tetap dapat mengedit nama kelas.
                        </small>
                    @endif
                </div>

                <div class="class-form-actions">
                    <a href="{{ route('kelas.index') }}" class="class-form-btn secondary">
                        Batal
                    </a>

                    <button type="submit" class="class-form-btn">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </section>

</div>
@endsection
