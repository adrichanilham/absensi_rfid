@extends('layouts.app')

@section('title', 'Daftarkan Kartu RFID')

@section('content')
<style>
    .rfid-wrapper {
        max-width: 780px;
        margin: 0 auto;
    }

    .rfid-back-link {
        display: inline-block;
        margin-bottom: 12px;
        color: #850000;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
    }

    .rfid-back-link:hover {
        text-decoration: underline;
    }

    .rfid-page-title {
        margin: 0 0 4px;
        color: #850000;
        font-family: Georgia, serif;
        font-size: 24px;
        font-weight: 700;
    }

    .rfid-page-subtitle {
        margin: 0 0 22px;
        color: #6b7280;
        font-size: 13px;
        line-height: 1.5;
    }

    .rfid-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .rfid-card-banner {
        background: #850000;
        padding: 16px 24px;
        color: #ffffff;
    }

    .rfid-card-banner h2 {
        margin: 0 0 3px;
        font-family: Georgia, serif;
        font-size: 16px;
        font-weight: 700;
        color: #ffffff;
    }

    .rfid-card-banner p {
        margin: 0;
        font-size: 12px;
        color: #f3d1d1;
    }

    .rfid-card-body {
        padding: 24px;
    }

    .rfid-form-group {
        margin-bottom: 20px;
    }

    .rfid-label {
        display: block;
        margin-bottom: 8px;
        font-size: 13px;
        font-weight: 700;
        color: #1f2937;
    }

    .rfid-input,
    .rfid-select {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
        color: #111827;
        background-color: #ffffff;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .rfid-input:focus,
    .rfid-select:focus {
        outline: none;
        border-color: #850000;
        box-shadow: 0 0 0 3px rgba(133, 0, 0, 0.12);
    }

    .rfid-help {
        margin: 6px 0 0;
        font-size: 12px;
        color: #6b7280;
        line-height: 1.5;
    }

    .rfid-error {
        margin-top: 6px;
        font-size: 12px;
        font-weight: 600;
        color: #b91c1c;
    }

    .rfid-alert {
        padding: 13px 16px;
        border-radius: 6px;
        background: #fef2f2;
        border: 1px solid #fca5a5;
        color: #991b1b;
        font-size: 13px;
        line-height: 1.5;
        margin-bottom: 20px;
    }

    .rfid-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        margin-top: 24px;
        padding-top: 16px;
        border-top: 1px solid #e5e7eb;
    }

    .rfid-btn-cancel {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 9px 18px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background: #ffffff;
        color: #374151;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: background 0.15s;
    }

    .rfid-btn-cancel:hover {
        background: #f9fafb;
    }

    .rfid-btn-submit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 9px 18px;
        border: 1px solid #850000;
        border-radius: 6px;
        background: #850000;
        color: #ffffff;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: background 0.15s;
    }

    .rfid-btn-submit:hover {
        background: #690000;
    }

    @media (max-width: 540px) {
        .rfid-actions {
            flex-direction: column-reverse;
        }

        .rfid-btn-cancel,
        .rfid-btn-submit {
            width: 100%;
        }
    }
</style>

<div class="rfid-wrapper">
    <a href="{{ route('rfid.index') }}" class="rfid-back-link">
        &larr; Kembali ke data RFID
    </a>

    <h1 class="rfid-page-title">Daftarkan Kartu RFID</h1>
    <p class="rfid-page-subtitle">
        Tambahkan UID kartu RFID dan hubungkan dengan data siswa.
    </p>

    <div class="rfid-card">
        <div class="rfid-card-banner">
            <h2>Informasi Kartu RFID</h2>
            <p>Lengkapi informasi kartu RFID pada formulir berikut</p>
        </div>

        <div class="rfid-card-body">
            @if ($errors->any())
                <div class="rfid-alert">
                    <strong>Periksa kembali data berikut:</strong>
                    @foreach ($errors->all() as $error)
                        <div style="margin-top: 4px;">• {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if ($siswa->isEmpty())
                <div class="rfid-alert">
                    Tidak ada siswa aktif yang belum memiliki kartu RFID.
                    Tambahkan siswa terlebih dahulu atau periksa kartu yang sudah terdaftar.
                </div>

                <div class="rfid-actions">
                    <a href="{{ route('rfid.index') }}" class="rfid-btn-cancel">
                        Kembali ke Data RFID
                    </a>
                    <a href="{{ route('siswa.create') }}" class="rfid-btn-submit">
                        Tambah Siswa
                    </a>
                </div>
            @else
                <form action="{{ route('rfid.store') }}" method="POST">
                    @csrf

                    <div class="rfid-form-group">
                        <label for="uid" class="rfid-label">UID Kartu RFID</label>
                        <input
                            type="text"
                            id="uid"
                            name="uid"
                            class="rfid-input"
                            value="{{ old('uid') }}"
                            placeholder="Contoh: 04A1B2C3D4"
                            maxlength="100"
                            autocomplete="off"
                            required
                            autofocus
                        >
                        <p class="rfid-help">
                            Tempelkan kartu pada reader jika reader dapat mengetik UID ke kolom ini, atau masukkan UID kartu secara manual.
                        </p>
                        @error('uid')
                            <div class="rfid-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="rfid-form-group">
                        <label for="siswa_id" class="rfid-label">Pilih Siswa</label>
                        <select id="siswa_id" name="siswa_id" class="rfid-select" required>
                            <option value="">-- Pilih siswa --</option>
                            @foreach ($siswa as $item)
                                <option
                                    value="{{ $item->id }}"
                                    @selected(old('siswa_id') == $item->id)
                                >
                                    {{ $item->nama_lengkap }}
                                    — NISN: {{ $item->nisn }}
                                    — {{ $item->kelas?->nama_kelas ?? 'Belum ada kelas' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="rfid-help">
                            Hanya siswa aktif yang belum memiliki kartu RFID yang ditampilkan.
                        </p>
                        @error('siswa_id')
                            <div class="rfid-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="rfid-actions">
                        <a href="{{ route('rfid.index') }}" class="rfid-btn-cancel">
                            Batal
                        </a>
                        <button type="submit" class="rfid-btn-submit">
                            Simpan Kartu RFID
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

<script>
    // Pindahkan fokus ke dropdown siswa saat reader USB otomatis menekan Enter setelah membaca UID
    const uidField = document.getElementById('uid');
    const siswaSelect = document.getElementById('siswa_id');

    if (uidField && siswaSelect) {
        uidField.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' && uidField.value.trim() !== '' && !siswaSelect.value) {
                event.preventDefault();
                siswaSelect.focus();
            }
        });
    }
</script>
@endsection