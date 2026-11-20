@extends('layouts.app')

@section('title', 'Edit Kartu RFID - Absensi RFID')

@section('page-heading', 'Edit Kartu RFID')

@section('content')

<style>
    .rfid-form-page {
        max-width: 760px;
        margin: 0 auto;
        color: #1f2937;
        font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, sans-serif;
        font-size: 13px;
    }

    .rfid-form-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 16px;
        color: #850000;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: color 0.18s ease;
    }

    .rfid-form-back:hover {
        color: #650000;
        text-decoration: underline;
    }

    .rfid-form-heading {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 18px 22px;
        margin-bottom: 20px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
    }

    .rfid-form-heading h2 {
        margin: 0 0 4px;
        color: #850000;
        font-family: inherit;
        font-size: 19px;
        font-weight: 700;
        letter-spacing: -0.015em;
    }

    .rfid-form-heading p {
        margin: 0;
        color: #6b7280;
        font-size: 13px;
        font-weight: 500;
        line-height: 1.5;
    }

    .rfid-form-card {
        overflow: hidden;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }

    .rfid-form-card-header {
        padding: 15px 20px;
        background: #850000;
        color: #ffffff;
    }

    .rfid-form-card-header h3 {
        margin: 0 0 3px;
        font-family: inherit;
        font-size: 15.5px;
        font-weight: 700;
        color: #ffffff;
    }

    .rfid-form-card-header p {
        margin: 0;
        color: #f3d1d1;
        font-size: 12px;
    }

    .rfid-form-card-body {
        padding: 22px 20px;
    }

    .rfid-form-group {
        margin-bottom: 18px;
    }

    .rfid-form-group:last-of-type {
        margin-bottom: 0;
    }

    .rfid-form-group label {
        display: block;
        margin-bottom: 6px;
        color: #374151;
        font-size: 12.5px;
        font-weight: 600;
    }

    .rfid-form-control {
        display: block;
        width: 100%;
        min-height: 40px;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #ffffff;
        color: #1f2937;
        font-family: inherit;
        font-size: 13px;
        outline: none;
        transition: border-color 0.18s ease, box-shadow 0.18s ease;
    }

    .rfid-form-control:focus {
        border-color: #850000;
        box-shadow: 0 0 0 3px rgba(133, 0, 0, 0.1);
    }

    /* Tampilan Pemilik Kartu model kaca tertutup (bersih tanpa badge merah) */
    .rfid-student-glass {
        position: relative;
        padding: 12px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: linear-gradient(145deg, rgba(248, 250, 252, 0.92), rgba(241, 245, 249, 0.85));
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.9), 0 1px 2px rgba(15, 23, 42, 0.03);
        color: #334155;
        pointer-events: none;
        user-select: none;
    }

    .rfid-student-glass strong {
        display: block;
        color: #1e293b;
        font-size: 13.5px;
        font-weight: 700;
        margin-bottom: 3px;
    }

    .rfid-student-glass span {
        display: block;
        color: #64748b;
        font-size: 12px;
    }

    .rfid-form-help {
        display: block;
        margin-top: 6px;
        color: #6b7280;
        font-size: 12px;
        line-height: 1.5;
    }

    .rfid-form-error {
        margin-top: 5px;
        color: #b91c1c;
        font-size: 12px;
        font-weight: 500;
    }

    .rfid-form-errors {
        margin-bottom: 16px;
        padding: 12px 15px;
        border: 1px solid #fecaca;
        border-radius: 8px;
        background: #fef2f2;
        color: #991b1b;
        font-size: 13px;
        font-weight: 500;
    }

    .rfid-form-errors ul {
        margin: 6px 0 0;
        padding-left: 18px;
    }

    .rfid-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 24px;
        padding-top: 16px;
        border-top: 1px solid #e5e7eb;
    }

    .rfid-form-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 0 16px;
        border: 1px solid #850000;
        border-radius: 8px;
        background: #850000;
        color: #ffffff;
        font-family: inherit;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.18s ease;
    }

    .rfid-form-btn:hover {
        background: #680000;
        border-color: #680000;
        color: #ffffff;
    }

    .rfid-form-btn.secondary {
        border-color: #d1d5db;
        background: #ffffff;
        color: #374151;
    }

    .rfid-form-btn.secondary:hover {
        background: #f9fafb;
        border-color: #9ca3af;
        color: #1f2937;
    }

    @media (max-width: 550px) {
        .rfid-form-heading {
            padding: 16px;
        }

        .rfid-form-card-body {
            padding: 16px;
        }

        .rfid-form-actions {
            flex-direction: column-reverse;
        }

        .rfid-form-btn {
            width: 100%;
        }
    }
</style>

<div class="rfid-form-page">

    <a href="{{ route('rfid.index') }}" class="rfid-form-back">
        ← Kembali ke data RFID
    </a>

    <div class="rfid-form-heading">
        <h2>Edit Kartu RFID</h2>
        <p>Perbarui UID kartu atau status aktif kartu RFID siswa.</p>
    </div>

    @if ($errors->any())
        <div class="rfid-form-errors">
            <strong>Periksa kembali data berikut:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="rfid-form-card">
        <div class="rfid-form-card-header">
            <h3>Informasi Kartu RFID</h3>
            <p>Perbarui informasi kartu pada formulir berikut</p>
        </div>

        <div class="rfid-form-card-body">
            <form action="{{ route('rfid.update', $rfid) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="rfid-form-group">
                    <label>Pemilik Kartu</label>

                    <input type="hidden" name="siswa_id" value="{{ $rfid->siswa_id }}">

                    <div class="rfid-student-glass">
                        <strong>{{ $rfid->siswa?->nama_lengkap ?? 'Siswa tidak ditemukan' }}</strong>
                        <span>
                            NISN: {{ $rfid->siswa?->nisn ?? '-' }}
                            <br>
                            Kelas: {{ $rfid->siswa?->kelas?->nama_kelas ?? 'Belum ada kelas' }}
                        </span>
                    </div>

                    @error('siswa_id')
                        <div class="rfid-form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="rfid-form-group">
                    <label for="uid">UID Kartu RFID</label>
                    <input
                        type="text"
                        id="uid"
                        name="uid"
                        class="rfid-form-control"
                        maxlength="100"
                        value="{{ old('uid', $rfid->uid) }}"
                        autocomplete="off"
                        required
                    >

                    <small class="rfid-form-help">
                        Tempelkan kartu pengganti pada reader jika ingin memperbarui UID kartu.
                    </small>

                    @error('uid')
                        <div class="rfid-form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="rfid-form-group">
                    <label for="aktif">Status Kartu</label>
                    <select id="aktif" name="aktif" class="rfid-form-control" required>
                        <option value="1"
                            @selected((string) old('aktif', (int) $rfid->aktif) === '1')>
                            Aktif
                        </option>

                        <option value="0"
                            @selected((string) old('aktif', (int) $rfid->aktif) === '0')>
                            Nonaktif
                        </option>
                    </select>

                    @error('aktif')
                        <div class="rfid-form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="rfid-form-actions">
                    <a href="{{ route('rfid.index') }}" class="rfid-form-btn secondary">
                        Batal
                    </a>

                    <button type="submit" class="rfid-form-btn">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </section>

</div>

@endsection