@extends('layouts.app')

@section('title', 'Riwayat Absensi Anak - Absensi RFID')

@section('page-heading', 'Riwayat Absensi Anak')

@section('content')

@php
    $tanggalIndonesia = now()->locale('id')->translatedFormat('l, d F Y');
@endphp

<style>
    .wali-history {
        color: #1f2937;
        font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, sans-serif;
        width: 100%;
    }

    .wali-heading {
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

    .wali-heading h2 {
        margin: 0 0 4px;
        color: #850000;
        font-family: inherit;
        font-size: 19px;
        font-weight: 700;
        letter-spacing: -0.015em;
    }

    .wali-heading p {
        margin: 0;
        color: #6b7280;
        font-family: inherit;
        font-size: 13px;
        font-weight: 500;
    }

    .wali-history-date {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 15px;
        border-radius: 8px;
        background: #fdf2f2;
        border: 1px solid #f5c6cb;
        color: #850000;
        font-family: inherit;
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
    }

    .wali-history-date svg {
        width: 17px;
        height: 17px;
        flex-shrink: 0;
    }

    .wali-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        margin-bottom: 20px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }

    .wali-card-header {
        padding: 15px 20px;
        background: #850000;
        color: #ffffff;
    }

    .wali-card-header h3 {
        margin: 0 0 3px;
        font-family: inherit;
        font-size: 15.5px;
        font-weight: 700;
        color: #ffffff;
    }

    .wali-card-header p {
        margin: 0;
        color: #f3d1d1;
        font-family: inherit;
        font-size: 12px;
    }

    .wali-card-body {
        padding: 18px 20px;
    }

    .wali-filter {
        display: flex;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 12px;
    }

    .wali-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 185px;
    }

    .wali-field label {
        color: #374151;
        font-family: inherit;
        font-size: 12px;
        font-weight: 600;
    }

    .wali-field input {
        width: 100%;
        height: 38px;
        padding: 7px 11px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #ffffff;
        color: #1f2937;
        font-family: inherit;
        font-size: 13px;
        outline: none;
        transition: border-color 0.18s ease, box-shadow 0.18s ease;
    }

    .wali-field input:focus {
        border-color: #850000;
        box-shadow: 0 0 0 3px rgba(133, 0, 0, 0.1);
    }

    .wali-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 38px;
        padding: 0 16px;
        border: 1px solid transparent;
        border-radius: 8px;
        background: #850000;
        color: #ffffff;
        font-family: inherit;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.18s ease;
    }

    .wali-button:hover {
        background: #680000;
        color: #ffffff;
    }

    .wali-button-reset {
        background: #ffffff;
        border-color: #d1d5db;
        color: #374151;
    }

    .wali-button-reset:hover {
        background: #f9fafb;
        border-color: #9ca3af;
        color: #1f2937;
    }

    .wali-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .wali-table {
        width: 100%;
        min-width: 650px;
        border-collapse: collapse;
        font-family: inherit;
    }

    .wali-table th {
        padding: 12px 14px;
        background: #f9fafb;
        color: #374151;
        font-size: 12px;
        font-weight: 700;
        text-align: left;
        white-space: nowrap;
        border-bottom: 1px solid #e5e7eb;
    }

    .wali-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #f3f4f6;
        color: #374151;
        font-size: 13px;
        vertical-align: middle;
    }

    .wali-table tbody tr:hover {
        background: #fdf8f8;
    }

    .wali-table tbody tr:last-child td {
        border-bottom: none;
    }

    .wali-status {
        display: inline-block;
        min-width: 58px;
        padding: 4px 10px;
        border-radius: 6px;
        background: #f3f4f6;
        color: #4b5563;
        text-align: center;
        font-size: 11.5px;
        font-weight: 700;
        text-transform: capitalize;
        white-space: nowrap;
    }

    .wali-status.status-hadir {
        background: #dcfce7;
        color: #15803d;
    }

    .wali-status.status-terlambat {
        background: #fef3c7;
        color: #b45309;
    }

    .wali-status.status-izin {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .wali-status.status-sakit {
        background: #cffafe;
        color: #0e7490;
    }

    .wali-status.status-alpa {
        background: #fee2e2;
        color: #b91c1c;
    }

    .wali-empty {
        padding: 32px 16px !important;
        color: #6b7280 !important;
        font-size: 13px !important;
        text-align: center;
    }

    .wali-error {
        display: block;
        margin-top: 4px;
        color: #b91c1c;
        font-family: inherit;
        font-size: 11.5px;
        font-weight: 500;
    }

    .wali-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        padding: 13px 18px;
        border-top: 1px solid #e5e7eb;
        background: #fafafa;
        font-family: inherit;
        font-size: 12px;
        color: #6b7280;
    }

    .wali-pagination nav {
        display: flex;
        justify-content: flex-end;
    }

    .wali-pagination nav > div:first-child {
        display: none;
    }

    .wali-pagination nav > div:last-child {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .wali-pagination nav a,
    .wali-pagination nav span[aria-current="page"] span,
    .wali-pagination nav span[aria-disabled="true"] span {
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

    .wali-pagination nav span[aria-current="page"] span {
        background: #850000;
        border-color: #850000;
        color: #ffffff;
    }

    .wali-pagination nav a:hover {
        background: #fdf2f2;
        border-color: #850000;
        color: #850000;
    }

    @media (max-width: 600px) {
        .wali-heading {
            padding: 16px;
            flex-direction: column;
            align-items: flex-start;
        }

        .wali-heading h2 {
            font-size: 17px;
        }

        .wali-history-date {
            font-size: 12px;
            padding: 7px 12px;
        }

        .wali-card-body {
            padding: 16px;
        }

        .wali-filter {
            align-items: stretch;
        }

        .wali-field {
            width: 100%;
        }

        .wali-button {
            flex: 1;
        }

        .wali-pagination {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="wali-history">

    <div class="wali-heading">
        <div>
            <h2>Riwayat Absensi Anak</h2>
            <p>
                {{ $anak->nama_lengkap }} —
                {{ $anak->kelas->nama_kelas ?? '-' }}
            </p>
        </div>

        <div class="wali-history-date">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            <span>{{ $tanggalIndonesia }}</span>
        </div>
    </div>

    <section class="wali-card">

        <div class="wali-card-header">
            <h3>Filter Tanggal</h3>
            <p>Pilih periode untuk melihat riwayat kehadiran anak</p>
        </div>

        <div class="wali-card-body">
            <form action="{{ route('wali.absensi.index') }}"
                  method="GET"
                  class="wali-filter">

                <div class="wali-field">
                    <label for="tanggal_mulai">Dari tanggal</label>
                    <input
                        type="date"
                        id="tanggal_mulai"
                        name="tanggal_mulai"
                        value="{{ request('tanggal_mulai') }}"
                    >

                    @error('tanggal_mulai')
                        <span class="wali-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="wali-field">
                    <label for="tanggal_selesai">Sampai tanggal</label>
                    <input
                        type="date"
                        id="tanggal_selesai"
                        name="tanggal_selesai"
                        value="{{ request('tanggal_selesai') }}"
                    >

                    @error('tanggal_selesai')
                        <span class="wali-error">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="wali-button">
                    Terapkan Filter
                </button>

                <a class="wali-button wali-button-reset"
                   href="{{ route('wali.absensi.index') }}">
                    Reset
                </a>

            </form>
        </div>
    </section>

    <section class="wali-card">

        <div class="wali-card-header">
            <h3>Daftar Kehadiran</h3>
            <p>Catatan kehadiran siswa berdasarkan periode yang dipilih</p>
        </div>

        <div class="wali-table-wrap">
            <table class="wali-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Tanggal</th>
                        <th>Waktu Masuk</th>
                        <th>Waktu Keluar</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($absensi as $item)
                        <tr>
                            <td>{{ $absensi->firstItem() + $loop->index }}</td>

                            <td>
                                {{ $item->tanggal->format('d-m-Y') }}
                            </td>

                            <td>{{ $item->waktu_masuk ?? '-' }}</td>

                            <td>{{ $item->waktu_keluar ?? '-' }}</td>

                            <td>
                                <span class="wali-status status-{{ $item->status }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>

                            <td>{{ $item->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="wali-empty">
                                Belum ada data absensi untuk periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="wali-pagination">
            <div>
                Menampilkan {{ $absensi->count() }} catatan pada halaman ini.
            </div>

            <div>
                {{ $absensi->links() }}
            </div>
        </div>

    </section>

</div>

@endsection