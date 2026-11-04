
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
@endphp

<style>
    .rfid-dashboard {
        font-family: Georgia, 'Times New Roman', serif;
        color: #242424;
        padding: 2px 2px 12px;
    }

    .rfid-welcome {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }

    .rfid-welcome h2 {
        margin: 0 0 4px;
        color: #850000;
        font-size: 18px;
        font-weight: bold;
    }

    .rfid-welcome p {
        margin: 0;
        color: #666;
        font-size: 12px;
        font-style: italic;
    }

    .rfid-date {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 12px;
        border-radius: 6px;
        background: #850000;
        color: white;
        font-size: 12px;
        font-weight: bold;
        white-space: nowrap;
    }

    .rfid-date-icon {
        font-size: 16px;
    }

    .rfid-statistics {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        justify-content: center;
        gap: 12px;
        width: 100%;
        max-width: 680px;
        margin: 0 auto 25px;
    }

    .rfid-stat-card {
        width: 100%;
        min-width: 0;
        height: 100px;
        padding: 10px;
        border: 1px solid #dedede;
        border-radius: 8px;
        background: #fafafa;
        box-shadow: 0 2px 4px rgba(0, 0, 0, .10);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        overflow: hidden;
    }

    .rfid-stat-top {
        display: flex;
        align-items: center;
        gap: 7px;
        min-width: 0;
    }

    .rfid-avatar {
        position: relative;
        flex: 0 0 38px;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: var(--stat-color);
    }

    .rfid-avatar::before {
        content: "";
        position: absolute;
        width: 14px;
        height: 14px;
        left: 12px;
        top: 6px;
        border-radius: 50%;
        background: white;
    }

    .rfid-avatar::after {
        content: "";
        position: absolute;
        width: 26px;
        height: 15px;
        left: 6px;
        bottom: 5px;
        border-radius: 18px 18px 12px 12px;
        background: white;
    }

    .rfid-stat-label {
        flex: 1;
        min-width: 0;
        color: var(--stat-color);
        font-size: 12px;
        font-weight: bold;
        line-height: 1.2;
        overflow-wrap: anywhere;
    }

    .rfid-stat-value {
        margin: 0;
        text-align: center;
        color: var(--stat-color);
        font-size: 21px;
        line-height: 1;
        font-weight: bold;
    }

    .rfid-total {
        --stat-color: #050505;
    }

    .rfid-hadir {
        --stat-color: #32bd24;
    }

    .rfid-terlambat {
        --stat-color: #e99419;
    }

    .rfid-belum {
        --stat-color: #d92720;
    }

    .rfid-table-section {
        margin-top: 0;
        border: 1px solid #ddd;
        border-radius: 7px;
        background: #fff;
        overflow: hidden;
    }

    .rfid-table-heading {
        padding: 11px 15px;
        border-bottom: 1px solid #ddd;
        background: #850000;
        color: white;
    }

    .rfid-table-heading h3 {
        margin: 0;
        font-size: 15px;
        font-weight: bold;
    }

    .rfid-table-heading p {
        margin: 3px 0 0;
        font-size: 10px;
        color: #f3dada;
    }

    .rfid-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .rfid-table {
        width: 100%;
        border-collapse: collapse;
        white-space: nowrap;
        font-family: Arial, sans-serif;
    }

    .rfid-table th {
        padding: 9px 12px;
        background: #f3eeee;
        color: #650000;
        font-size: 11px;
        text-align: left;
        font-weight: bold;
    }

    .rfid-table td {
        padding: 9px 12px;
        border-bottom: 1px solid #eee;
        color: #444;
        font-size: 11px;
    }

    .rfid-table tbody tr:hover {
        background: #fff8f8;
    }

    .rfid-table tbody tr:last-child td {
        border-bottom: none;
    }

    .rfid-status {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: bold;
        text-transform: capitalize;
    }

    .rfid-status.status-hadir {
        background: #dcfce7;
        color: #15803d;
    }

    .rfid-status.status-terlambat {
        background: #ffedd5;
        color: #c2410c;
    }

    .rfid-status.status-izin {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .rfid-status.status-sakit {
        background: #cffafe;
        color: #0891b2;
    }

    .rfid-status.status-alpa {
        background: #fee2e2;
        color: #b91c1c;
    }

    .rfid-status.status-default {
        background: #eee;
        color: #444;
    }

    .rfid-empty {
        padding: 20px !important;
        text-align: center;
        color: #888 !important;
    }

    @media (max-width: 900px) {
        .rfid-statistics {
            grid-template-columns: repeat(2, minmax(130px, 160px));
            max-width: 360px;
            gap: 12px;
        }

        .rfid-stat-card {
            height: 100px;
        }
    }

    @media (max-width: 600px) {
        .rfid-dashboard {
            padding: 0;
        }

        .rfid-welcome {
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .rfid-welcome h2 {
            font-size: 16px;
        }

        .rfid-date {
            font-size: 10px;
            padding: 7px 9px;
        }

        .rfid-statistics {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            max-width: 340px;
            gap: 10px;
            margin-bottom: 20px;
        }

        .rfid-stat-card {
            height: 95px;
        }

        .rfid-table-heading {
            padding: 10px 12px;
        }
    }

    @media (max-width: 350px) {
        .rfid-statistics {
            grid-template-columns: 1fr;
            max-width: 180px;
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
            <span class="rfid-date-icon">🗓️</span>
            {{ $hariIni->translatedFormat('l, d F Y') }}
        </div>
    </div>

    <!-- KARTU STATISTIK -->
    <div class="rfid-statistics">

        <div class="rfid-stat-card rfid-total">
            <div class="rfid-stat-top">
                <div class="rfid-avatar"></div>
                <div class="rfid-stat-label">Total Siswa</div>
            </div>
            <div class="rfid-stat-value">{{ $totalSiswa }}</div>
        </div>

        <div class="rfid-stat-card rfid-hadir">
            <div class="rfid-stat-top">
                <div class="rfid-avatar"></div>
                <div class="rfid-stat-label">Hadir</div>
            </div>
            <div class="rfid-stat-value">{{ $hadirHariIni }}</div>
        </div>

        <div class="rfid-stat-card rfid-terlambat">
            <div class="rfid-stat-top">
                <div class="rfid-avatar"></div>
                <div class="rfid-stat-label">Terlambat</div>
            </div>
            <div class="rfid-stat-value">{{ $terlambatHariIni }}</div>
        </div>

        <div class="rfid-stat-card rfid-belum">
            <div class="rfid-stat-top">
                <div class="rfid-avatar"></div>
                <div class="rfid-stat-label">Belum Ada Catatan</div>
            </div>
            <div class="rfid-stat-value">{{ $belumAdaCatatan }}</div>
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
