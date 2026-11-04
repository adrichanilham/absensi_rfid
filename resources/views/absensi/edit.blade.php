
@extends('layouts.app')

@section('title', 'Koreksi Absensi')
@section('page-heading', 'Koreksi Absensi')

@section('content')
<style>
    .correction-page {
        max-width: 850px;
        margin: 0 auto;
        color: #292929;
        font-family: Arial, sans-serif;
        font-size: 13px;
    }

    .correction-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 16px;
        color: #850000;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
    }

    .correction-back:hover {
        color: #650000;
        text-decoration: underline;
    }

    .correction-heading {
        margin-bottom: 18px;
    }

    .correction-heading h2 {
        margin: 0 0 5px;
        color: #850000;
        font-family: Georgia, serif;
        font-size: 21px;
    }

    .correction-heading p {
        margin: 0;
        color: #777;
        font-size: 12px;
    }

    .correction-card {
        overflow: hidden;
        margin-bottom: 16px;
        border: 1px solid #e8e1e1;
        border-radius: 9px;
        background: #fff;
    }

    .correction-card-header {
        padding: 13px 16px;
        background: #850000;
        color: #fff;
    }

    .correction-card-header h3 {
        margin: 0 0 4px;
        font-family: Georgia, serif;
        font-size: 15px;
    }

    .correction-card-header p {
        margin: 0;
        color: #f4dddd;
        font-size: 11px;
    }

    .correction-card-body {
        padding: 16px;
    }

    .student-info {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .student-info-item {
        padding: 11px 12px;
        border: 1px solid #eee5e5;
        border-radius: 6px;
        background: #fcfafa;
        min-width: 0;
    }

    .student-info-item span {
        display: block;
        margin-bottom: 5px;
        color: #777;
        font-size: 11px;
    }

    .student-info-item strong {
        display: block;
        color: #333;
        font-size: 13px;
        overflow-wrap: anywhere;
    }

    .correction-form {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .form-group {
        min-width: 0;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        color: #555;
        font-size: 12px;
        font-weight: 600;
    }

    .form-control {
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

    .form-control:focus {
        border-color: #850000;
        box-shadow: 0 0 0 3px rgba(133, 0, 0, .08);
    }

    textarea.form-control {
        min-height: 95px;
        resize: vertical;
    }

    .correction-errors {
        margin-bottom: 15px;
        padding: 11px 14px;
        border: 1px solid #f1caca;
        border-radius: 7px;
        background: #fff5f5;
        color: #b91c1c;
        font-size: 12px;
    }

    .correction-errors ul {
        margin: 6px 0 0;
        padding-left: 18px;
    }

    .correction-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 16px;
    }

    .correction-btn {
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

    .correction-btn:hover {
        background: #690000;
        color: #fff;
    }

    .correction-btn.secondary {
        border-color: #ddd;
        background: #fff;
        color: #555;
    }

    .correction-btn.secondary:hover {
        background: #f7f7f7;
    }

    @media (max-width: 600px) {
        .correction-heading h2 {
            font-size: 18px;
        }

        .correction-card-body {
            padding: 12px;
        }

        .student-info,
        .correction-form {
            grid-template-columns: 1fr;
        }

        .form-group.full-width {
            grid-column: auto;
        }

        .correction-actions {
            flex-direction: column-reverse;
        }

        .correction-btn {
            width: 100%;
        }
    }
</style>

<div class="correction-page">

    <a class="correction-back" href="{{ route('absensi.index') }}">
        ← Kembali ke data absensi
    </a>

    <div class="correction-heading">
        <h2>Koreksi Absensi</h2>
        <p>Perbarui status dan informasi kehadiran siswa.</p>
    </div>

    @if ($errors->any())
        <div class="correction-errors">
            <strong>Periksa kembali data berikut:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="correction-card">
        <div class="correction-card-header">
            <h3>Informasi Siswa</h3>
            <p>Data siswa dan tanggal absensi yang sedang dikoreksi</p>
        </div>

        <div class="correction-card-body">
            <div class="student-info">
                <div class="student-info-item">
                    <span>Nama siswa</span>
                    <strong>{{ $absensi->siswa->nama_lengkap }}</strong>
                </div>

                <div class="student-info-item">
                    <span>NISN</span>
                    <strong>{{ $absensi->siswa->nisn }}</strong>
                </div>

                <div class="student-info-item">
                    <span>Kelas</span>
                    <strong>{{ $absensi->siswa->kelas->nama_kelas ?? '-' }}</strong>
                </div>

                <div class="student-info-item">
                    <span>Tanggal absensi</span>
                    <strong>{{ $absensi->tanggal->format('d-m-Y') }}</strong>
                </div>
            </div>
        </div>
    </section>

    <section class="correction-card">
        <div class="correction-card-header">
            <h3>Form Koreksi</h3>
            <p>Sesuaikan informasi kehadiran, lalu simpan perubahan</p>
        </div>

        <div class="correction-card-body">
            <form method="POST" action="{{ route('absensi.update', $absensi) }}">
                @csrf
                @method('PUT')

                <div class="correction-form">
                    <div class="form-group">
                        <label for="status">Status kehadiran</label>
                        <select id="status" name="status" class="form-control" required>
                            @foreach ([
                                'hadir' => 'Hadir',
                                'terlambat' => 'Terlambat',
                                'sakit' => 'Sakit',
                                'izin' => 'Izin',
                                'alpa' => 'Alpa',
                            ] as $value => $label)
                                <option value="{{ $value }}"
                                    @selected(old('status', $absensi->status) === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="waktu_masuk">Waktu masuk</label>
                        <input
                            id="waktu_masuk"
                            type="time"
                            name="waktu_masuk"
                            class="form-control"
                            value="{{ old('waktu_masuk', $absensi->waktu_masuk ? substr((string) $absensi->waktu_masuk, 0, 5) : '') }}">
                    </div>

                    <div class="form-group">
                        <label for="waktu_keluar">Waktu keluar</label>
                        <input
                            id="waktu_keluar"
                            type="time"
                            name="waktu_keluar"
                            class="form-control"
                            value="{{ old('waktu_keluar', $absensi->waktu_keluar ? substr((string) $absensi->waktu_keluar, 0, 5) : '') }}">
                    </div>

                    <div class="form-group full-width">
                        <label for="keterangan">Keterangan</label>
                        <textarea
                            id="keterangan"
                            name="keterangan"
                            rows="4"
                            class="form-control">{{ old('keterangan', $absensi->keterangan) }}</textarea>
                    </div>
                </div>

                <div class="correction-actions">
                    <a href="{{ route('absensi.index') }}" class="correction-btn secondary">
                        Batal
                    </a>

                    <button type="submit" class="correction-btn">
                        Simpan perubahan
                    </button>
                </div>
            </form>
        </div>
    </section>

</div>
@endsection
