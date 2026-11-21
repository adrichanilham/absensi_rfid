@extends('layouts.app')

@section('title', 'Form Pengajuan Izin/Sakit')

@section('content')
<style>
    .izin-wrapper {
        max-width: 780px;
        margin: 0 auto;
    }

    .izin-back-link {
        display: inline-block;
        margin-bottom: 12px;
        color: #850000;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
    }

    .izin-back-link:hover {
        text-decoration: underline;
    }

    .izin-page-title {
        margin: 0 0 4px;
        color: #850000;
        font-family: Georgia, serif;
        font-size: 24px;
        font-weight: 700;
    }

    .izin-page-subtitle {
        margin: 0 0 22px;
        color: #6b7280;
        font-size: 13px;
        line-height: 1.5;
    }

    .izin-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .izin-card-banner {
        background: #850000;
        padding: 16px 24px;
        color: #ffffff;
    }

    .izin-card-banner h2 {
        margin: 0 0 3px;
        font-family: Georgia, serif;
        font-size: 16px;
        font-weight: 700;
        color: #ffffff;
    }

    .izin-card-banner p {
        margin: 0;
        font-size: 12px;
        color: #f3d1d1;
    }

    .izin-card-body {
        padding: 24px;
    }

    .izin-student-info {
        background: #fdf2f2;
        border: 1px solid #f5c6cb;
        border-left: 4px solid #850000;
        border-radius: 6px;
        padding: 14px 18px;
        margin-bottom: 18px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 8px 16px;
    }

    .izin-student-info p {
        margin: 0;
        font-size: 13px;
        color: #374151;
        line-height: 1.5;
    }

    .izin-student-info strong {
        color: #850000;
    }

    .izin-required-note {
        margin: 0 0 20px;
        font-size: 12px;
        color: #6b7280;
    }

    .required-star {
        color: #dc2626;
        font-weight: 700;
        margin-left: 3px;
    }

    .izin-form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .izin-form-group {
        margin-bottom: 20px;
    }

    .izin-label {
        display: block;
        margin-bottom: 8px;
        font-size: 13px;
        font-weight: 700;
        color: #1f2937;
    }

    .izin-input,
    .izin-select,
    .izin-textarea {
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

    .izin-input[type="file"] {
        padding: 9px 12px;
        background-color: #f9fafb;
        cursor: pointer;
    }

    .izin-input:focus,
    .izin-select:focus,
    .izin-textarea:focus {
        outline: none;
        border-color: #850000;
        box-shadow: 0 0 0 3px rgba(133, 0, 0, 0.12);
    }

    .izin-textarea {
        resize: vertical;
        min-height: 115px;
        line-height: 1.5;
    }

    .izin-help {
        margin: 6px 0 0;
        font-size: 12px;
        color: #6b7280;
        line-height: 1.5;
    }

    .izin-alert {
        padding: 13px 16px;
        border-radius: 6px;
        background: #fef2f2;
        border: 1px solid #fca5a5;
        color: #991b1b;
        font-size: 13px;
        line-height: 1.5;
        margin-bottom: 20px;
    }

    .izin-alert ul {
        margin: 6px 0 0;
        padding-left: 18px;
    }

    .izin-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        margin-top: 24px;
        padding-top: 16px;
        border-top: 1px solid #e5e7eb;
    }

    .izin-btn-cancel {
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

    .izin-btn-cancel:hover {
        background: #f9fafb;
    }

    .izin-btn-submit {
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

    .izin-btn-submit:hover {
        background: #690000;
    }

    @media (max-width: 600px) {
        .izin-form-row {
            grid-template-columns: 1fr;
            gap: 0;
        }

        .izin-actions {
            flex-direction: column-reverse;
        }

        .izin-btn-cancel,
        .izin-btn-submit {
            width: 100%;
        }
    }
</style>

<div class="izin-wrapper">
    <a href="{{ route('wali.pengajuan.index') }}" class="izin-back-link">
        &larr; Kembali ke daftar pengajuan
    </a>

    <h1 class="izin-page-title">Form Pengajuan Izin/Sakit</h1>
    <p class="izin-page-subtitle">
        Isi formulir berikut dengan benar. Pengajuan akan dikirim kepada operator untuk diverifikasi.
    </p>

    <div class="izin-card">
        <div class="izin-card-banner">
            <h2>Informasi Pengajuan Izin / Sakit</h2>
            <p>Lengkapi detail ketidakhadiran siswa pada formulir berikut</p>
        </div>

        <div class="izin-card-body">
            <div class="izin-student-info">
                <p><strong>Nama Siswa:</strong> {{ $anak->nama_lengkap }}</p>
                <p><strong>NISN:</strong> {{ $anak->NISN }}</p>
                <p><strong>Kelas:</strong> {{ $anak->kelas->nama_kelas ?? '-' }}</p>
            </div>

            <p class="izin-required-note">
                Kolom bertanda <span class="required-star">*</span> wajib diisi.
            </p>

            @if ($errors->any())
                <div class="izin-alert">
                    <strong>Pengajuan belum dapat dikirim:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                action="{{ route('wali.pengajuan.store') }}"
                method="POST"
                enctype="multipart/form-data"
            >
                @csrf

                <div class="izin-form-group">
                    <label for="jenis" class="izin-label">
                        Jenis Pengajuan <span class="required-star">*</span>
                    </label>
                    <select id="jenis" name="jenis" class="izin-select" required>
                        <option value="">-- Pilih Jenis --</option>
                        <option value="izin" @selected(old('jenis') === 'izin')>
                            Izin
                        </option>
                        <option value="sakit" @selected(old('jenis') === 'sakit')>
                            Sakit
                        </option>
                    </select>
                </div>

                <div class="izin-form-row">
                    <div class="izin-form-group">
                        <label for="tanggal_mulai" class="izin-label">
                            Tanggal Mulai <span class="required-star">*</span>
                        </label>
                        <input
                            type="date"
                            id="tanggal_mulai"
                            name="tanggal_mulai"
                            class="izin-input"
                            value="{{ old('tanggal_mulai') }}"
                            required
                        >
                    </div>

                    <div class="izin-form-group">
                        <label for="tanggal_selesai" class="izin-label">
                            Tanggal Selesai <span class="required-star">*</span>
                        </label>
                        <input
                            type="date"
                            id="tanggal_selesai"
                            name="tanggal_selesai"
                            class="izin-input"
                            value="{{ old('tanggal_selesai') }}"
                            required
                        >
                        <p class="izin-help">
                            Tanggal selesai tidak boleh lebih awal daripada tanggal mulai.
                        </p>
                    </div>
                </div>

                <div class="izin-form-group">
                    <label for="alasan" class="izin-label">
                        Alasan Izin/Sakit <span class="required-star">*</span>
                    </label>
                    <textarea
                        id="alasan"
                        name="alasan"
                        class="izin-textarea"
                        maxlength="1000"
                        placeholder="Jelaskan alasan pengajuan..."
                        required
                    >{{ old('alasan') }}</textarea>
                </div>

                <div class="izin-form-group">
                    <label for="dokumen" class="izin-label">Dokumen Pendukung (Opsional)</label>
                    <input
                        type="file"
                        id="dokumen"
                        name="dokumen"
                        class="izin-input"
                        accept=".pdf,.jpg,.jpeg,.png"
                    >
                    <p class="izin-help">
                        Format yang diperbolehkan: PDF, JPG, JPEG, PNG. Maksimal 2 MB.
                    </p>
                </div>

                <div class="actions izin-actions">
                    <a href="{{ route('wali.pengajuan.index') }}" class="izin-btn-cancel">
                        Batal
                    </a>
                    <button type="submit" class="izin-btn-submit">
                        Kirim Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const tglMulai = document.getElementById('tanggal_mulai');
    const tglSelesai = document.getElementById('tanggal_selesai');

    if (tglMulai && tglSelesai) {
        tglMulai.addEventListener('change', function () {
            tglSelesai.min = this.value;
            if (tglSelesai.value && tglSelesai.value < this.value) {
                tglSelesai.value = this.value;
            }
        });
    }
</script>
@endsection