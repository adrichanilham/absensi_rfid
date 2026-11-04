
@extends('layouts.app')

@section('title', 'Data Absensi - Absensi RFID')

@section('page-heading', 'Data Absensi')

@section('content')

@php
    $isOperator = auth()->user()->role === 'operator';
@endphp

<style>
    .attendance-page {
        font-family: Arial, sans-serif;
        color: #292929;
        width: 100%;
    }

    /* HEADER HALAMAN */
    .attendance-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .attendance-heading h2 {
        margin: 0 0 5px;
        color: #850000;
        font-family: Georgia, serif;
        font-size: 20px;
    }

    .attendance-heading p {
        margin: 0;
        color: #777;
        font-family: Georgia, serif;
        font-size: 12px;
        font-style: italic;
    }

    .attendance-date {
        display: flex;
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
    .attendance-alert {
        padding: 11px 14px;
        margin-bottom: 15px;
        border-radius: 7px;
        font-size: 13px;
    }

    .attendance-alert.success {
        background: #dcfce7;
        color: #166534;
    }

    .attendance-alert.error {
        background: #fee2e2;
        color: #991b1b;
    }

    /* FILTER */
    .filter-panel {
        background: #fff;
        border: 1px solid #e5dddd;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 18px;
    }

    .filter-title {
        color: #850000;
        font-family: Georgia, serif;
        font-size: 15px;
        font-weight: bold;
        margin: 0 0 13px;
    }

    .attendance-filters {
        display: grid;
        grid-template-columns: 1.1fr 1fr 1fr auto;
        align-items: end;
        gap: 10px;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 0;
    }

    .filter-group label {
        color: #555;
        font-size: 11px;
        font-weight: 600;
    }

    .filter-group input,
    .filter-group select {
        width: 100%;
        height: 36px;
        padding: 7px 9px;
        border: 1px solid #ddd;
        border-radius: 6px;
        background: #fff;
        color: #333;
        font-family: Arial, sans-serif;
        font-size: 12px;
        outline: none;
    }

    .filter-group input:focus,
    .filter-group select:focus {
        border-color: #850000;
        box-shadow: 0 0 0 2px rgba(133,0,0,.08);
    }

    .filter-actions {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .btn-attendance {
        height: 36px;
        padding: 0 13px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        border: 1px solid transparent;
        border-radius: 6px;
        text-decoration: none;
        font-size: 11px;
        font-weight: bold;
        cursor: pointer;
        white-space: nowrap;
        transition: .2s;
    }

    .btn-filter {
        background: #850000;
        color: white;
    }

    .btn-filter:hover {
        background: #680000;
    }

    .btn-reset {
        background: white;
        color: #850000;
        border-color: #e3d0d0;
    }

    .btn-reset:hover {
        background: #fff1f1;
    }

    /* TABEL */
    .attendance-table-panel {
        background: white;
        border: 1px solid #e5dddd;
        border-radius: 8px;
        overflow: hidden;
    }

    .attendance-table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding: 13px 15px;
        background: #850000;
        color: white;
    }

    .attendance-table-header h3 {
        margin: 0 0 4px;
        font-family: Georgia, serif;
        font-size: 16px;
    }

    .attendance-table-header p {
        margin: 0;
        color: #f1dada;
        font-size: 10px;
    }

    .attendance-count {
        padding: 6px 9px;
        border-radius: 5px;
        background: rgba(255,255,255,.15);
        color: white;
        font-size: 10px;
    }

    .attendance-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .attendance-table {
        width: 100%;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
        min-width: 850px;
    }

    .attendance-table thead th {
        padding: 11px 10px;
        background: #f3eeee;
        color: #700000;
        font-size: 11px;
        font-weight: bold;
        text-align: left;
        white-space: nowrap;
        border-bottom: 1px solid #e6dede;
    }

    .attendance-table tbody td {
        padding: 10px;
        border-bottom: 1px solid #eee;
        color: #444;
        font-size: 11px;
        vertical-align: middle;
    }

    .attendance-table tbody tr:hover {
        background: #fff8f8;
    }

    .attendance-table tbody tr:last-child td {
        border-bottom: none;
    }

    .student-name {
        color: #292929;
        font-weight: bold;
    }

    .student-nisn {
        display: block;
        margin-top: 3px;
        color: #999;
        font-size: 9px;
    }

    /* BADGE STATUS */
    .status-badge {
        display: inline-block;
        min-width: 48px;
        padding: 5px 8px;
        border-radius: 20px;
        text-align: center;
        font-size: 10px;
        font-weight: bold;
        text-transform: capitalize;
    }

    .status-hadir {
        background: #dcfce7;
        color: #15803d;
    }

    .status-terlambat {
        background: #ffedd5;
        color: #c2410c;
    }

    .status-izin {
        background: #fef3c7;
        color: #a16207;
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
        background: #eee;
        color: #555;
    }

    /* TOMBOL KOREKSI */
    .btn-correction {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        padding: 6px 10px;
        border: 1px solid #e6caca;
        border-radius: 5px;
        background: white;
        color: #850000;
        text-decoration: none;
        font-size: 10px;
        font-weight: bold;
        white-space: nowrap;
        transition: .2s;
    }

    .btn-correction:hover {
        background: #850000;
        border-color: #850000;
        color: white;
    }

    /* DATA KOSONG */
    .attendance-empty {
        padding: 35px 15px !important;
        text-align: center;
        color: #999 !important;
        font-size: 12px !important;
    }

    /* PAGINATION */
    .attendance-pagination {
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

    .attendance-pagination nav span[aria-current="page"] span {
        background: #850000;
        border-color: #850000;
        color: white;
    }

    .attendance-pagination nav a:hover {
        background: #fff1f1;
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
            align-items: flex-start;
        }

        .attendance-heading h2 {
            font-size: 18px;
        }

        .attendance-date {
            font-size: 10px;
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
            <span>🗓️</span>
            {{ now()->translatedFormat('l, d F Y') }}
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
