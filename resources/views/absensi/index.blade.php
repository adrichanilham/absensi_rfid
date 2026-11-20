@extends('layouts.app')

@section('title', 'Data Absensi - Absensi RFID')

@section('page-heading', 'Data Absensi')

@section('content')

@php
    $isOperator = auth()->user()->role === 'operator';
    $tanggalIndonesia = now()->locale('id')->translatedFormat('l, d F Y');
@endphp

<style>
    .attendance-page {
        font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, sans-serif;
        color: #1f2937;
        width: 100%;
    }

    /* HEADER HALAMAN */
    .attendance-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 18px 22px;
        margin-bottom: 20px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
    }

    .attendance-heading h2 {
        margin: 0 0 4px;
        color: #850000;
        font-family: inherit;
        font-size: 19px;
        font-weight: 700;
        letter-spacing: -0.015em;
    }

    .attendance-heading p {
        margin: 0;
        color: #6b7280;
        font-family: inherit;
        font-size: 13px;
        font-weight: 500;
    }

    .attendance-date {
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

    .attendance-date svg {
        width: 17px;
        height: 17px;
        flex-shrink: 0;
    }

    /* NOTIFIKASI */
    .attendance-alert {
        padding: 12px 15px;
        margin-bottom: 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
    }

    .attendance-alert.success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
    }

    .attendance-alert.error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }

    /* FILTER */
    .filter-panel {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 18px 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
    }

    .filter-title {
        color: #850000;
        font-family: inherit;
        font-size: 15px;
        font-weight: 700;
        margin: 0 0 14px;
    }

    .attendance-filters {
        display: grid;
        grid-template-columns: 1.1fr 1fr 1fr auto;
        align-items: end;
        gap: 12px;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 0;
    }

    .filter-group label {
        color: #374151;
        font-size: 12px;
        font-weight: 600;
    }

    .filter-group input,
    .filter-group select {
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
        transition: border-color 0.18s, box-shadow 0.18s;
    }

    .filter-group input:focus,
    .filter-group select:focus {
        border-color: #850000;
        box-shadow: 0 0 0 3px rgba(133, 0, 0, 0.1);
    }

    .filter-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-attendance {
        height: 38px;
        padding: 0 15px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
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

    .btn-filter {
        background: #850000;
        color: #ffffff;
    }

    .btn-filter:hover {
        background: #680000;
    }

    .btn-reset {
        background: #ffffff;
        color: #374151;
        border-color: #d1d5db;
    }

    .btn-reset:hover {
        background: #f9fafb;
        border-color: #9ca3af;
    }

    /* TABEL */
    .attendance-table-panel {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }

    .attendance-table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding: 15px 20px;
        background: #850000;
        color: #ffffff;
    }

    .attendance-table-header h3 {
        margin: 0 0 3px;
        font-family: inherit;
        font-size: 15.5px;
        font-weight: 700;
        color: #ffffff;
    }

    .attendance-table-header p {
        margin: 0;
        color: #f3d1d1;
        font-size: 12px;
    }

    .attendance-count {
        padding: 6px 12px;
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.16);
        color: #ffffff;
        font-size: 12px;
        font-weight: 600;
    }

    .attendance-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .attendance-table {
        width: 100%;
        border-collapse: collapse;
        font-family: inherit;
        min-width: 850px;
    }

    .attendance-table thead th {
        padding: 12px 14px;
        background: #f9fafb;
        color: #374151;
        font-size: 12px;
        font-weight: 700;
        text-align: left;
        white-space: nowrap;
        border-bottom: 1px solid #e5e7eb;
    }

    .attendance-table tbody td {
        padding: 12px 14px;
        border-bottom: 1px solid #f3f4f6;
        color: #374151;
        font-size: 13px;
        vertical-align: middle;
    }

    .attendance-table tbody tr:hover {
        background: #fdf8f8;
    }

    .attendance-table tbody tr:last-child td {
        border-bottom: none;
    }

    .student-name {
        color: #111827;
        font-weight: 600;
    }

    .student-nisn {
        display: block;
        margin-top: 3px;
        color: #6b7280;
        font-size: 11px;
    }

    /* BADGE STATUS */
    .status-badge {
        display: inline-block;
        min-width: 56px;
        padding: 4px 10px;
        border-radius: 6px;
        text-align: center;
        font-size: 11.5px;
        font-weight: 700;
        text-transform: capitalize;
    }

    .status-hadir {
        background: #dcfce7;
        color: #15803d;
    }

    .status-terlambat {
        background: #fef3c7;
        color: #b45309;
    }

    .status-izin {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .status-sakit {
        background: #cffafe;
        color: #0e7490;
    }

    .status-alpa {
        background: #fee2e2;
        color: #b91c1c;
    }

    .status-default {
        background: #f3f4f6;
        color: #4b5563;
    }

    /* TOMBOL KOREKSI */
    .btn-correction {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        padding: 6px 12px;
        border: 1px solid #e6caca;
        border-radius: 6px;
        background: #ffffff;
        color: #850000;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
        transition: all 0.18s ease;
    }

    .btn-correction:hover {
        background: #850000;
        border-color: #850000;
        color: #ffffff;
    }

    /* DATA KOSONG */
    .attendance-empty {
        padding: 32px 16px !important;
        text-align: center;
        color: #6b7280 !important;
        font-size: 13px !important;
    }

    /* PAGINATION */
    .attendance-pagination {
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

    .attendance-pagination nav {
        display: flex;
        justify-content: flex-end;
    }

    .attendance-pagination nav > div:first-child {
        display: none;
    }

    .attendance-pagination nav > div:last-child {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .attendance-pagination nav a,
    .attendance-pagination nav span[aria-current="page"] span,
    .attendance-pagination nav span[aria-disabled="true"] span {
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

    .attendance-pagination nav span[aria-current="page"] span {
        background: #850000;
        border-color: #850000;
        color: #ffffff;
    }

    .attendance-pagination nav a:hover {
        background: #fdf2f2;
        border-color: #850000;
        color: #850000;
    }

    /* RESPONSIVE */
    @media (max-width: 900px) {
        .attendance-filters {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .filter-actions {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 550px) {
        .attendance-heading {
            padding: 16px;
            flex-direction: column;
            align-items: flex-start;
        }

        .attendance-heading h2 {
            font-size: 17px;
        }

        .attendance-date {
            font-size: 12px;
            padding: 7px 12px;
        }

        .attendance-filters {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            grid-column: auto;
        }

        .filter-actions .btn-attendance {
            flex: 1;
        }

        .attendance-table-header {
            align-items: flex-start;
        }

        .attendance-pagination {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="attendance-page">

    <!-- JUDUL DAN TANGGAL -->
    <div class="attendance-heading">
        <div>
            <h2>Data Absensi</h2>
            <p>Pantau dan kelola kehadiran siswa.</p>
        </div>

        <div class="attendance-date">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            <span>{{ $tanggalIndonesia }}</span>
        </div>
    </div>

    <!-- FILTER ABSENSI -->
    <section class="filter-panel">

        <h3 class="filter-title">Filter Data Absensi</h3>

        <form method="GET" action="{{ route('absensi.index') }}"
              class="attendance-filters">

            <div class="filter-group">
                <label for="tanggal">Tanggal Absensi</label>
                <input
                    type="date"
                    id="tanggal"
                    name="tanggal"
                    value="{{ $filters['tanggal'] ?? '' }}">
            </div>

            <div class="filter-group">
                <label for="kelas_id">Kelas</label>
                <select name="kelas_id" id="kelas_id">
                    <option value="">Semua kelas yang diizinkan</option>

                    @foreach ($kelas as $item)
                        <option
                            value="{{ $item->id }}"
                            @selected((string) ($filters['kelas_id'] ?? '') === (string) $item->id)>
                            {{ $item->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label for="status">Status Kehadiran</label>
                <select name="status" id="status">
                    <option value="">Semua status</option>

                    @foreach ([
                        'hadir' => 'Hadir',
                        'terlambat' => 'Terlambat',
                        'sakit' => 'Sakit',
                        'izin' => 'Izin',
                        'alpa' => 'Alpa',
                    ] as $value => $label)

                        <option
                            value="{{ $value }}"
                            @selected(($filters['status'] ?? '') === $value)>
                            {{ $label }}
                        </option>

                    @endforeach
                </select>
            </div>

            <div class="filter-actions">

                <button type="submit"
                        class="btn-attendance btn-filter">
                    ☷ Tampilkan
                </button>

                <a href="{{ route('absensi.index') }}"
                   class="btn-attendance btn-reset">
                    Reset
                </a>

            </div>

        </form>
    </section>

    <!-- TABEL DAFTAR KEHADIRAN -->
    <section class="attendance-table-panel">

        <div class="attendance-table-header">

            <div>
                <h3>Daftar Kehadiran</h3>
                <p>Data absensi siswa berdasarkan filter yang dipilih</p>
            </div>

            <div class="attendance-count">
                {{ $absensi->count() }} catatan di halaman ini
            </div>

        </div>

        <div class="attendance-table-wrap">

            <table class="attendance-table">

                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Tanggal</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Status</th>
                        <th>Keterangan</th>

                        @if ($isOperator)
                            <th>Tindakan</th>
                        @endif
                    </tr>
                </thead>

                <tbody>

                    @forelse ($absensi as $item)

                        <tr>

                            <td>{{ $loop->iteration }}</td>

                            <td>
                                {{ $item->tanggal->format('d-m-Y') }}
                            </td>

                            <td>
                                {{ $item->siswa->nisn }}
                            </td>

                            <td>
                                <span class="student-name">
                                    {{ $item->siswa->nama_lengkap }}
                                </span>
                            </td>

                            <td>
                                {{ $item->siswa->kelas->nama_kelas ?? '-' }}
                            </td>

                            <td>
                                {{ $item->waktu_masuk ?: '-' }}
                            </td>

                            <td>
                                {{ $item->waktu_keluar ?: '-' }}
                            </td>

                            <td>
                                @php
                                    $statusClass = in_array($item->status, [
                                        'hadir',
                                        'terlambat',
                                        'izin',
                                        'sakit',
                                        'alpa'
                                    ]) ? 'status-' . $item->status : 'status-default';
                                @endphp

                                <span class="status-badge {{ $statusClass }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>

                            <td>
                                {{ $item->keterangan ?: '-' }}
                            </td>

                            @if ($isOperator)
                                <td>
                                    <a class="btn-correction"
                                       href="{{ route('absensi.edit', $item) }}">
                                        ✎ Koreksi
                                    </a>
                                </td>
                            @endif

                        </tr>

                    @empty

                        <tr>
                            <td colspan="{{ $isOperator ? 10 : 9 }}"
                                class="attendance-empty">
                                Belum ada catatan absensi untuk filter ini.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <!-- PAGINATION -->
        <div class="attendance-pagination">

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