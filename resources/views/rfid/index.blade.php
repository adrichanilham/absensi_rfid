
@extends('layouts.app')

@section('title', 'Data RFID - Absensi RFID')

@section('page-heading', 'Data RFID')

@section('content')

<style>
    .rfid-page {
        width: 100%;
        font-family: Arial, sans-serif;
        color: #292929;
    }

    /* HEADER HALAMAN */
    .rfid-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 20px;
    }

    .rfid-heading h2 {
        margin: 0 0 5px;
        color: #850000;
        font-family: Georgia, serif;
        font-size: 20px;
    }

    .rfid-heading p {
        margin: 0;
        color: #777;
        font-family: Georgia, serif;
        font-size: 12px;
        font-style: italic;
    }

    .rfid-date {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 13px;
        background: #850000;
        color: white;
        border-radius: 7px;
        font-family: Georgia, serif;
        font-size: 12px;
        font-weight: bold;
    }

    /* NOTIFIKASI */
    .rfid-alert {
        padding: 11px 14px;
        margin-bottom: 15px;
        border-radius: 7px;
        font-size: 13px;
    }

    .rfid-alert.success {
        background: #dcfce7;
        color: #166534;
    }

    .rfid-alert.error {
        background: #fee2e2;
        color: #991b1b;
    }

    /* PANEL TABEL */
    .rfid-table-panel {
        background: white;
        border: 1px solid #e5dddd;
        border-radius: 8px;
        overflow: hidden;
    }

    .rfid-table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding: 13px 15px;
        background: #850000;
        color: white;
    }

    .rfid-table-header h3 {
        margin: 0 0 4px;
        font-family: Georgia, serif;
        font-size: 16px;
    }

    .rfid-table-header p {
        margin: 0;
        color: #f1dada;
        font-size: 10px;
    }

    /* TOMBOL */
    .rfid-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px 12px;
        border: 1px solid transparent;
        border-radius: 6px;
        text-decoration: none;
        font-family: Arial, sans-serif;
        font-size: 11px;
        font-weight: bold;
        cursor: pointer;
        white-space: nowrap;
        transition: .2s;
    }

    .rfid-btn-add {
        background: white;
        color: #850000;
    }

    .rfid-btn-add:hover {
        background: #fff0f0;
    }

    .rfid-btn-edit {
        background: white;
        color: #850000;
        border-color: #e6caca;
    }

    .rfid-btn-edit:hover {
        background: #850000;
        color: white;
        border-color: #850000;
    }

    .rfid-btn-delete {
        background: white;
        color: #b91c1c;
        border-color: #f1caca;
    }

    .rfid-btn-delete:hover {
        background: #b91c1c;
        color: white;
        border-color: #b91c1c;
    }

    /* TABEL */
    .rfid-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .rfid-table {
        width: 100%;
        min-width: 800px;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
    }

    .rfid-table thead th {
        padding: 11px 10px;
        background: #f3eeee;
        color: #700000;
        font-size: 11px;
        font-weight: bold;
        text-align: left;
        white-space: nowrap;
        border-bottom: 1px solid #e6dede;
    }

    .rfid-table tbody td {
        padding: 11px 10px;
        border-bottom: 1px solid #eee;
        color: #444;
        font-size: 11px;
        vertical-align: middle;
    }

    .rfid-table tbody tr:hover {
        background: #fff8f8;
    }

    .rfid-table tbody tr:last-child td {
        border-bottom: none;
    }

    .rfid-uid {
        color: #850000;
        font-weight: bold;
        font-family: monospace;
        font-size: 12px;
    }

    .rfid-student-name {
        color: #292929;
        font-weight: bold;
    }

    .rfid-student-nisn {
        display: block;
        margin-top: 3px;
        color: #999;
        font-size: 9px;
    }

    /* BADGE STATUS */
    .rfid-status {
        display: inline-block;
        min-width: 60px;
        padding: 5px 9px;
        border-radius: 20px;
        text-align: center;
        font-size: 10px;
        font-weight: bold;
    }

    .rfid-status.active {
        background: #dcfce7;
        color: #15803d;
    }

    .rfid-status.inactive {
        background: #fee2e2;
        color: #b91c1c;
    }

    /* AKSI */
    .rfid-actions {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .rfid-actions form {
        display: inline-flex;
        margin: 0;
    }

    /* DATA KOSONG */
    .rfid-empty {
        padding: 35px 15px !important;
        text-align: center;
        color: #999 !important;
        font-size: 12px !important;
    }

    /* PAGINATION */
    .rfid-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        padding: 12px 15px;
        border-top: 1px solid #eee;
        font-size: 11px;
        color: #777;
    }

    .rfid-pagination nav {
        display: flex;
        justify-content: flex-end;
    }

    .rfid-pagination nav > div:first-child {
        display: none;
    }

    .rfid-pagination nav > div:last-child {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .rfid-pagination nav a,
    .rfid-pagination nav span[aria-current="page"] span,
    .rfid-pagination nav span[aria-disabled="true"] span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 28px;
        height: 28px;
        padding: 0 8px;
        border: 1px solid #e5dddd;
        border-radius: 5px;
        color: #850000;
        background: white;
        text-decoration: none;
        font-size: 10px;
    }

    .rfid-pagination nav span[aria-current="page"] span {
        background: #850000;
        border-color: #850000;
        color: white;
    }

    .rfid-pagination nav a:hover {
        background: #fff1f1;
    }

    /* RESPONSIVE */
    @media (max-width: 600px) {
        .rfid-heading h2 {
            font-size: 18px;
        }

        .rfid-date {
            font-size: 10px;
        }

        .rfid-table-header {
            align-items: flex-start;
        }

        .rfid-pagination {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="rfid-page">

    <!-- JUDUL DAN TANGGAL -->
    <div class="rfid-heading">

        <div>
            <h2>Data RFID</h2>
            <p>Kelola kartu RFID yang terhubung dengan data siswa.</p>
        </div>

        <div class="rfid-date">
            <span>🗓️</span>
            {{ now()->translatedFormat('l, d F Y') }}
        </div>

    </div>

    <!-- NOTIFIKASI -->
    @if (session('success'))
        <div class="rfid-alert success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rfid-alert error">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rfid-alert error">
            <strong>Periksa kembali data berikut:</strong>

            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <!-- TABEL DATA RFID -->
    <section class="rfid-table-panel">

        <div class="rfid-table-header">

            <div>
                <h3>Daftar Kartu RFID</h3>
                <p>Informasi UID kartu dan siswa yang terdaftar</p>
            </div>

            <a href="{{ route('rfid.create') }}"
               class="rfid-btn rfid-btn-add">
                + Daftarkan Kartu
            </a>

        </div>

        <div class="rfid-table-wrapper">

            <table class="rfid-table">

                <thead>
                    <tr>
                        <th>No.</th>
                        <th>UID RFID</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Status Kartu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($rfid as $index => $kartu)

                        <tr>

                            <td>
                                {{ $rfid->firstItem() + $index }}
                            </td>

                            <td>
                                <span class="rfid-uid">
                                    {{ $kartu->uid }}
                                </span>
                            </td>

                            <td>
                                {{ $kartu->siswa?->nisn ?? '-' }}
                            </td>

                            <td>
                                <span class="rfid-student-name">
                                    {{ $kartu->siswa?->nama_lengkap ?? 'Siswa tidak ditemukan' }}
                                </span>
                            </td>

                            <td>
                                {{ $kartu->siswa?->kelas?->nama_kelas ?? '-' }}
                            </td>

                            <td>
                                @if ($kartu->aktif)
                                    <span class="rfid-status active">
                                        Aktif
                                    </span>
                                @else
                                    <span class="rfid-status inactive">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td>
                                <div class="rfid-actions">

                                    <a href="{{ route('rfid.edit', $kartu) }}"
                                       class="rfid-btn rfid-btn-edit">
                                        ✎ Edit
                                    </a>

                                    @if ($kartu->aktif)

                                        <form
                                            action="{{ route('rfid.destroy', $kartu) }}"
                                            method="POST"
                                            onsubmit="return confirm('Nonaktifkan kartu RFID ini?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="rfid-btn rfid-btn-delete">
                                                Nonaktifkan
                                            </button>
                                        </form>

                                    @endif

                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="rfid-empty">
                                Belum ada kartu RFID yang terdaftar.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <!-- PAGINATION -->
        <div class="rfid-pagination">

            <div>
                Menampilkan {{ $rfid->count() }} kartu RFID pada halaman ini.
            </div>

            <div>
                {{ $rfid->links() }}
            </div>

        </div>

    </section>

</div>

@endsection
