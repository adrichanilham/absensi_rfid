@extends('layouts.app')

@section('title', 'Data RFID - Absensi RFID')

@section('page-heading', 'Data RFID')

@section('content')

@php
    $tanggalIndonesia = now()->locale('id')->translatedFormat('l, d F Y');
@endphp

<style>
    .rfid-page {
        width: 100%;
        font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, sans-serif;
        color: #1f2937;
    }
    .rfid-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 18px 22px;
        margin-bottom: 20px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
    }

    .rfid-heading h2 {
        margin: 0 0 4px;
        color: #850000;
        font-family: inherit;
        font-size: 19px;
        font-weight: 700;
        letter-spacing: -0.015em;
    }

    .rfid-heading p {
        margin: 0;
        color: #6b7280;
        font-family: inherit;
        font-size: 13px;
        font-weight: 500;
    }

    .rfid-date {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 15px;
        background: #fdf2f2;
        border: 1px solid #f5c6cb;
        color: #850000;
        border-radius: 8px;
        font-family: inherit;
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
    }

    .rfid-date svg {
        width: 17px;
        height: 17px;
        flex-shrink: 0;
    }

    /* NOTIFIKASI */
    .rfid-alert {
        padding: 12px 15px;
        margin-bottom: 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
    }

    .rfid-alert.success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
    }

    .rfid-alert.error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }

    /* PANEL TABEL */
    .rfid-table-panel {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }

    .rfid-table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding: 15px 20px;
        background: #850000;
        color: #ffffff;
    }

    .rfid-table-header h3 {
        margin: 0 0 3px;
        font-family: inherit;
        font-size: 15.5px;
        font-weight: 700;
        color: #ffffff;
    }

    .rfid-table-header p {
        margin: 0;
        color: #f3d1d1;
        font-size: 12px;
    }
    .rfid-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px 14px;
        border: 1px solid transparent;
        border-radius: 8px;
        text-decoration: none;
        font-family: inherit;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.18s ease;
    }

    .rfid-btn-add {
        background: #ffffff;
        color: #850000;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .rfid-btn-add:hover {
        background: #fdf2f2;
    }

    .rfid-btn-edit {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        background: #ffffff;
        color: #850000;
        border-color: #e6caca;
    }

    .rfid-btn-edit:hover {
        background: #850000;
        color: #ffffff;
        border-color: #850000;
    }

    .rfid-btn-delete {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        background: #ffffff;
        color: #b91c1c;
        border-color: #fecaca;
    }

    .rfid-btn-delete:hover {
        background: #b91c1c;
        color: #ffffff;
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
        font-family: inherit;
    }

    .rfid-table thead th {
        padding: 12px 14px;
        background: #f9fafb;
        color: #374151;
        font-size: 12px;
        font-weight: 700;
        text-align: left;
        white-space: nowrap;
        border-bottom: 1px solid #e5e7eb;
    }

    .rfid-table tbody td {
        padding: 12px 14px;
        border-bottom: 1px solid #f3f4f6;
        color: #374151;
        font-size: 13px;
        vertical-align: middle;
    }

    .rfid-table tbody tr:hover {
        background: #fdf8f8;
    }

    .rfid-table tbody tr:last-child td {
        border-bottom: none;
    }

    .rfid-uid {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 6px;
        background: #fdf2f2;
        border: 1px solid #f5c6cb;
        color: #850000;
        font-weight: 700;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 12.5px;
        letter-spacing: 0.03em;
    }

    .rfid-student-name {
        color: #111827;
        font-weight: 600;
    }

    /* BADGE STATUS */
    .rfid-status {
        display: inline-block;
        min-width: 64px;
        padding: 4px 10px;
        border-radius: 6px;
        text-align: center;
        font-size: 11.5px;
        font-weight: 700;
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

    .rfid-empty {
        padding: 32px 16px !important;
        text-align: center;
        color: #6b7280 !important;
        font-size: 13px !important;
    }

    /* PAGINATION */
    .rfid-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        padding: 13px 18px;
        border-top: 1px solid #e5e7eb;
        background: #fafafa;
        font-size: 12px;
        color: #6b7280;
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
        min-width: 30px;
        height: 30px;
        padding: 0 9px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        color: #374151;
        background: #ffffff;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
    }

    .rfid-pagination nav span[aria-current="page"] span {
        background: #850000;
        border-color: #850000;
        color: #ffffff;
    }

    .rfid-pagination nav a:hover {
        background: #fdf2f2;
        border-color: #850000;
        color: #850000;
    }

    /* RESPONSIVE */
    @media (max-width: 600px) {
        .rfid-heading {
            padding: 16px;
            flex-direction: column;
            align-items: flex-start;
        }

        .rfid-heading h2 {
            font-size: 17px;
        }

        .rfid-date {
            font-size: 12px;
            padding: 7px 12px;
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

    <div class="rfid-heading">

        <div>
            <h2>Data RFID</h2>
            <p>Kelola kartu RFID yang terhubung dengan data siswa.</p>
        </div>

        <div class="rfid-date">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            <span>{{ $tanggalIndonesia }}</span>
        </div>

    </div>

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