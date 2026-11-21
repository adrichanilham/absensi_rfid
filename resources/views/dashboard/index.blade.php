@extends('layouts.app')

@section('title', 'Dashboard - Absensi RFID')

@section('page-heading', 'Dashboard')

@section('content')

@php
    $user = auth()->user();

    $roleGreetings = [
        'admin' => 'Selamat Datang, Administrator!',
        'operator' => 'Selamat Datang, Operator!',
        'guru' => 'Selamat Datang, Bapak/Ibu Guru!',
        'wali_murid' => 'Selamat Datang, Wali Murid!',
    ];

    $greeting = $roleGreetings[$user->role] ?? 'Selamat Datang!';
    $tanggalIndonesia = \Carbon\Carbon::parse($hariIni)->locale('id')->translatedFormat('l, d F Y');
@endphp

<style>
    .rfid-dashboard {
        font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, sans-serif;
        color: #1f2937;
    }

    /* ================= HEADER SAMBUTAN & TANGGAL ================= */

    .rfid-welcome {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 18px 22px;
        margin-bottom: 22px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
    }

    .rfid-welcome h2 {
        margin: 0 0 4px;
        color: #850000;
        font-size: 19px;
        font-weight: 700;
        letter-spacing: -0.015em;
    }

    .rfid-welcome p {
        margin: 0;
        color: #6b7280;
        font-size: 13px;
        font-weight: 500;
    }

    .rfid-date {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 15px;
        border-radius: 8px;
        background: #fdf2f2;
        border: 1px solid #f5c6cb;
        color: #850000;
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
    }

    .rfid-date svg {
        width: 17px;
        height: 17px;
        flex-shrink: 0;
    }

    /* ================= KARTU STATISTIK ================= */

    .rfid-statistics {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        width: 100%;
        margin-bottom: 24px;
    }

    .rfid-stat-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-left: 4px solid var(--stat-color);
        border-radius: 12px;
        padding: 18px 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        display: flex;
        align-items: center;
        gap: 15px;
        min-width: 0;
        transition: transform 0.18s ease, box-shadow 0.18s ease;
    }

    .rfid-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
    }

    .rfid-stat-icon {
        width: 48px;
        height: 48px;
        min-width: 48px;
        border-radius: 12px;
        background: var(--stat-bg);
        color: var(--stat-color);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .rfid-stat-icon svg {
        width: 24px;
        height: 24px;
        stroke-width: 2.1;
    }

    .rfid-stat-body {
        min-width: 0;
        flex: 1;
    }

    .rfid-stat-label {
        color: #6b7280;
        font-size: 12.5px;
        font-weight: 600;
        line-height: 1.3;
        margin-bottom: 4px;
    }

    .rfid-stat-value {
        margin: 0;
        color: #111827;
        font-size: 26px;
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: -0.02em;
    }

    .rfid-total {
        --stat-color: #850000;
        --stat-bg: #fdf2f2;
    }

    .rfid-hadir {
        --stat-color: #16a34a;
        --stat-bg: #dcfce7;
    }

    .rfid-terlambat {
        --stat-color: #d97706;
        --stat-bg: #fef3c7;
    }

    .rfid-belum {
        --stat-color: #dc2626;
        --stat-bg: #fee2e2;
    }

    /* ================= TABEL ABSENSI TERBARU ================= */

    .rfid-table-section {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }

    .rfid-table-heading {
        padding: 15px 20px;
        background: #850000;
        color: #ffffff;
    }

    .rfid-table-heading h3 {
        margin: 0;
        font-size: 15.5px;
        font-weight: 700;
        color: #ffffff;
    }

    .rfid-table-heading p {
        margin: 3px 0 0;
        font-size: 12px;
        color: #f3d1d1;
    }

    .rfid-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .rfid-table {
        width: 100%;
        border-collapse: collapse;
        white-space: nowrap;
        font-family: inherit;
    }

    .rfid-table th {
        padding: 12px 16px;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        color: #374151;
        font-size: 12px;
        font-weight: 700;
        text-align: left;
    }

    .rfid-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #f3f4f6;
        color: #374151;
        font-size: 13px;
    }

    .rfid-table tbody tr:hover {
        background: #fdf8f8;
    }

    .rfid-table tbody tr:last-child td {
        border-bottom: none;
    }

    .rfid-status {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11.5px;
        font-weight: 700;
        text-transform: capitalize;
    }

    .rfid-status.status-hadir {
        background: #dcfce7;
        color: #15803d;
    }

    .rfid-status.status-terlambat {
        background: #fef3c7;
        color: #b45309;
    }

    .rfid-status.status-izin {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .rfid-status.status-sakit {
        background: #cffafe;
        color: #0e7490;
    }

    .rfid-status.status-alpa {
        background: #fee2e2;
        color: #b91c1c;
    }

    .rfid-status.status-default {
        background: #f3f4f6;
        color: #4b5563;
    }

    .rfid-empty {
        padding: 28px 16px !important;
        text-align: center;
        color: #6b7280 !important;
        font-size: 13px;
    }

    /* ================= RESPONSIVE ================= */

    @media (max-width: 1024px) {
        .rfid-statistics {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }
    }

    @media (max-width: 600px) {
        .rfid-welcome {
            padding: 16px;
            flex-direction: column;
            align-items: flex-start;
        }

        .rfid-welcome h2 {
            font-size: 17px;
        }

        .rfid-date {
            font-size: 12px;
            padding: 7px 12px;
        }

        .rfid-statistics {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .rfid-stat-card {
            padding: 14px;
            gap: 11px;
        }

        .rfid-stat-icon {
            width: 40px;
            height: 40px;
            min-width: 40px;
            border-radius: 10px;
        }

        .rfid-stat-icon svg {
            width: 20px;
            height: 20px;
        }

        .rfid-stat-value {
            font-size: 22px;
        }
    }

    @media (max-width: 380px) {
        .rfid-statistics {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="rfid-dashboard">

    <!-- SAMBUTAN DAN TANGGAL -->
    <div class="rfid-welcome">
        <div>
            <h2>{{ $greeting }}</h2>
            <p>
                Halo, {{ $user->username }}. Berikut ringkasan data absensi hari ini.
            </p>
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

    <!-- KARTU STATISTIK -->
    <div class="rfid-statistics">

        <div class="rfid-stat-card rfid-total">
            <div class="rfid-stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div class="rfid-stat-body">
                <div class="rfid-stat-label">Total Siswa</div>
                <div class="rfid-stat-value">{{ $totalSiswa }}</div>
            </div>
        </div>

        <div class="rfid-stat-card rfid-hadir">
            <div class="rfid-stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <polyline points="17 11 19 13 23 9"></polyline>
                </svg>
            </div>
            <div class="rfid-stat-body">
                <div class="rfid-stat-label">Hadir</div>
                <div class="rfid-stat-value">{{ $hadirHariIni }}</div>
            </div>
        </div>

        <div class="rfid-stat-card rfid-terlambat">
            <div class="rfid-stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <div class="rfid-stat-body">
                <div class="rfid-stat-label">Terlambat</div>
                <div class="rfid-stat-value">{{ $terlambatHariIni }}</div>
            </div>
        </div>

        <div class="rfid-stat-card rfid-belum">
            <div class="rfid-stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <line x1="23" y1="11" x2="17" y2="11"></line>
                </svg>
            </div>
            <div class="rfid-stat-body">
                <div class="rfid-stat-label">Belum Ada Catatan</div>
                <div class="rfid-stat-value">{{ $belumAdaCatatan }}</div>
            </div>
        </div>

    </div>

    <!-- TABEL ABSENSI TERBARU -->
    <section class="rfid-table-section">

        <div class="rfid-table-heading">
            <h3>Absensi Terbaru Hari Ini</h3>
            <p>Daftar kehadiran siswa pada hari ini</p>
        </div>

        <div class="rfid-table-wrapper">
            <table class="rfid-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th>Kelas</th>
                        <th>Waktu Masuk</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($absensiTerbaru as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>
                                {{ $item->siswa?->nama_lengkap ?? 'Siswa tidak ditemukan' }}
                            </td>

                            <td>{{ $item->siswa?->nisn ?? '-' }}</td>

                            <td>
                                {{ $item->siswa?->kelas?->nama_kelas ?? '-' }}
                            </td>

                            <td>
                                {{ $item->waktu_masuk
                                    ? substr((string) $item->waktu_masuk, 0, 8)
                                    : '-' }}
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

                                <span class="rfid-status {{ $statusClass }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="rfid-empty">
                                Belum ada catatan absensi hari ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </section>

</div>

@endsection