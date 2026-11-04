
@extends('layouts.app')

@section('title', 'Verifikasi Pengajuan')
@section('page-heading', 'Verifikasi Pengajuan')

@section('content')
<style>
    .verification-page {
        color: #333;
        font-family: Arial, sans-serif;
        font-size: 12px;
    }

    .verification-heading {
        margin-bottom: 18px;
    }

    .verification-heading h2 {
        margin: 0 0 5px;
        color: #850000;
        font-family: Georgia, serif;
        font-size: 19px;
        font-weight: 700;
    }

    .verification-heading p {
        margin: 0;
        color: #888;
        font-size: 11px;
    }

    .verification-alert {
        padding: 11px 14px;
        margin-bottom: 14px;
        border: 1px solid;
        border-radius: 6px;
        font-size: 11px;
    }

    .verification-alert.success {
        border-color: #cce8d5;
        background: #f0faf3;
        color: #267443;
    }

    .verification-alert.error {
        border-color: #f0cccc;
        background: #fff5f5;
        color: #a52b2b;
    }

    .verification-alert ul {
        margin: 7px 0 0 17px;
    }

    .verification-filter {
        display: flex;
        flex-wrap: wrap;
        align-items: end;
        gap: 10px;
        padding: 14px;
        margin-bottom: 16px;
        border: 1px solid #e9e5e5;
        border-radius: 7px;
        background: #fff;
    }

    .verification-filter label {
        display: flex;
        flex-direction: column;
        gap: 6px;
        color: #666;
        font-size: 10px;
        font-weight: 600;
    }

    .verification-filter select {
        width: 210px;
        min-height: 34px;
        padding: 7px 10px;
        border: 1px solid #dedede;
        border-radius: 5px;
        background: #fff;
        color: #444;
        font-size: 11px;
        outline: none;
    }

    .verification-filter select:focus,
    .verification-page textarea:focus {
        border-color: #850000;
    }

    .verification-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        min-height: 32px;
        padding: 0 11px;
        border: 1px solid transparent;
        border-radius: 5px;
        cursor: pointer;
        font-family: Arial, sans-serif;
        font-size: 10px;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
    }

    .verification-btn.primary {
        background: #850000;
        color: #fff;
    }

    .verification-btn.primary:hover {
        background: #690000;
    }

    .verification-btn.secondary {
        border-color: #ddd;
        background: #fff;
        color: #555;
    }

    .verification-btn.secondary:hover {
        background: #f7f7f7;
    }

    .verification-btn.approve {
        background: #168044;
        color: #fff;
    }

    .verification-btn.approve:hover {
        background: #116536;
    }

    .verification-btn.reject {
        background: #b32626;
        color: #fff;
    }

    .verification-btn.reject:hover {
        background: #8f1c1c;
    }

    .verification-table-card {
        overflow: hidden;
        border: 1px solid #e8e5e5;
        border-radius: 8px;
        background: #fff;
    }

    .verification-table-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 13px 15px;
        border-bottom: 1px solid #eee;
    }

    .verification-table-heading h3 {
        margin: 0 0 4px;
        color: #850000;
        font-family: Georgia, serif;
        font-size: 13px;
    }

    .verification-table-heading p {
        margin: 0;
        color: #999;
        font-size: 9px;
    }

    .verification-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .verification-table {
        width: 100%;
        min-width: 1050px;
        border-collapse: collapse;
        font-size: 10px;
    }

    .verification-table th {
        padding: 10px 9px;
        background: #f8eeee;
        color: #666;
        font-size: 9px;
        font-weight: 600;
        text-align: left;
        white-space: nowrap;
    }

    .verification-table td {
        padding: 11px 9px;
        border-bottom: 1px solid #eee;
        color: #444;
        vertical-align: top;
        line-height: 1.6;
    }

    .verification-table tbody tr:hover {
        background: #fcf9f9;
    }

    .verification-table tbody tr:last-child td {
        border-bottom: none;
    }

    .verification-table strong {
        color: #333;
    }

    .verification-muted {
        color: #999;
        font-size: 9px;
    }

    .verification-reason {
        min-width: 150px;
        max-width: 220px;
        overflow-wrap: anywhere;
    }

    .verification-reason a {
        display: inline-block;
        margin-top: 7px;
        color: #850000;
        font-size: 10px;
        font-weight: 600;
        text-decoration: none;
    }

    .verification-reason a:hover {
        text-decoration: underline;
    }

    .verification-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 9px;
        font-weight: 700;
        white-space: nowrap;
    }

    .verification-badge.menunggu {
        background: #fff4d8;
        color: #a16b00;
    }

    .verification-badge.disetujui {
        background: #e3f5e9;
        color: #168044;
    }

    .verification-badge.ditolak {
        background: #fce7e7;
        color: #b32626;
    }

    .verification-note {
        min-width: 165px;
        min-height: 65px;
        margin: 6px 0 8px;
        padding: 8px 9px;
        border: 1px solid #dedede;
        border-radius: 5px;
        background: #fff;
        color: #444;
        font-family: Arial, sans-serif;
        font-size: 10px;
        resize: vertical;
    }

    .verification-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }

    .verification-actions .verification-btn {
        min-height: 28px;
        padding: 0 9px;
        font-size: 9px;
    }

    .verification-empty {
        padding: 35px 15px !important;
        text-align: center;
        color: #999 !important;
    }

    .verification-empty strong {
        display: block;
        margin-bottom: 5px;
        color: #850000;
        font-family: Georgia, serif;
        font-size: 14px;
    }

    .verification-pagination {
        padding: 13px 15px;
        border-top: 1px solid #eee;
    }

    .verification-pagination nav {
        display: flex;
        justify-content: flex-end;
    }

    @media (max-width: 600px) {
        .verification-filter {
            align-items: stretch;
        }

        .verification-filter > div {
            width: 100%;
        }

        .verification-filter select {
            width: 100%;
        }

        .verification-table-heading {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>

<div class="verification-page">

    <div class="verification-heading">
        <h2>Verifikasi Pengajuan Izin/Sakit</h2>
        <p>
            Periksa data pengajuan Wali Murid, lalu setujui atau tolak pengajuan.
        </p>
    </div>

    @if (session('success'))
        <div class="verification-alert success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="verification-alert error">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="verification-alert error">
            <strong>Periksa kembali data verifikasi:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('operator.pengajuan.index') }}"
          method="GET"
          class="verification-filter">

        <div>
            <label for="status">Filter Status</label>
            <select name="status" id="status">
                <option value="">Semua Status</option>
                <option value="menunggu"
                    @selected(request('status') === 'menunggu')>
                    Menunggu
                </option>
                <option value="disetujui"
                    @selected(request('status') === 'disetujui')>
                    Disetujui
                </option>
                <option value="ditolak"
                    @selected(request('status') === 'ditolak')>
                    Ditolak
                </option>
            </select>
        </div>

        <button type="submit" class="verification-btn primary">
            Terapkan Filter
        </button>

        <a href="{{ route('operator.pengajuan.index') }}"
           class="verification-btn secondary">
            Reset
        </a>
    </form>

    <section class="verification-table-card">

        <div class="verification-table-heading">
            <div>
                <h3>Daftar Pengajuan Wali Murid</h3>
                <p>Periksa informasi dan berikan keputusan verifikasi</p>
            </div>
        </div>

        @if ($pengajuan->count())
            <div class="verification-table-wrap">
                <table class="verification-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Data Siswa</th>
                            <th>Periode</th>
                            <th>Jenis</th>
                            <th>Alasan dan Dokumen</th>
                            <th>Status</th>
                            <th>Catatan Verifikasi</th>
                            <th>Keputusan Operator</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($pengajuan as $item)
                            <tr>
                                <td>
                                    {{ $pengajuan->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    <strong>
                                        {{ $item->siswa?->nama_lengkap ?? 'Siswa tidak ditemukan' }}
                                    </strong>
                                    <br>
                                    <span class="verification-muted">
                                        NISN: {{ $item->siswa?->NISN ?? '-' }}
                                    </span>
                                    <br>
                                    <span class="verification-muted">
                                        Kelas: {{ $item->siswa?->kelas?->nama_kelas ?? '-' }}
                                    </span>
                                </td>

                                <td>
                                    {{ $item->tanggal_mulai->format('d-m-Y') }}
                                    <br>
                                    <span class="verification-muted">sampai</span>
                                    <br>
                                    {{ $item->tanggal_selesai->format('d-m-Y') }}
                                </td>

                                <td>
                                    {{ ucfirst($item->jenis) }}
                                </td>

                                <td class="verification-reason">
                                    {{ $item->alasan }}

                                    <br>

                                    @if ($item->dokumen_path)
                                        <a href="{{ route('operator.pengajuan.dokumen', $item) }}">
                                            Unduh dokumen
                                        </a>
                                    @else
                                        <span class="verification-muted">
                                            Tanpa dokumen
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <span class="verification-badge {{ $item->status }}">
                                        {{ ucfirst($item->status) }}
                                    </span>

                                    @if ($item->diverifikasi_pada)
                                        <br><br>
                                        <span class="verification-muted">
                                            {{ $item->diverifikasi_pada->format('d-m-Y H:i') }}
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    {{ $item->catatan_verifikasi ?: '-' }}

                                    @if ($item->verifier)
                                        <br><br>
                                        <span class="verification-muted">
                                            Oleh: {{ $item->verifier->username }}
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if ($item->status === 'menunggu')
                                        <form
                                            action="{{ route('operator.pengajuan.verifikasi', $item) }}"
                                            method="POST"
                                        >
                                            @csrf
                                            @method('PUT')

                                            <label for="catatan-{{ $item->id }}">
                                                Catatan Operator
                                            </label>

                                            <textarea
                                                class="verification-note"
                                                id="catatan-{{ $item->id }}"
                                                name="catatan_verifikasi"
                                                maxlength="1000"
                                                placeholder="Catatan opsional..."
                                            >{{ old('catatan_verifikasi') }}</textarea>

                                            <div class="verification-actions">
                                                <button
                                                    type="submit"
                                                    name="status"
                                                    value="disetujui"
                                                    class="verification-btn approve"
                                                    onclick="return confirm('Setujui pengajuan ini?')"
                                                >
                                                    Setujui
                                                </button>

                                                <button
                                                    type="submit"
                                                    name="status"
                                                    value="ditolak"
                                                    class="verification-btn reject"
                                                    onclick="return confirm('Tolak pengajuan ini?')"
                                                >
                                                    Tolak
                                                </button>
                                            </div>
                                        </form>
                                    @else
                                        <span class="verification-muted">
                                            Pengajuan sudah diputuskan.
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="verification-pagination">
                {{ $pengajuan->links() }}
            </div>
        @else
            <div class="verification-empty">
                <strong>Belum Ada Pengajuan</strong>
                <span>
                    Pengajuan yang sesuai dengan filter akan muncul di sini.
                </span>
            </div>
        @endif

    </section>

</div>
@endsection
