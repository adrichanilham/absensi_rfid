@extends('layouts.app')

@section('title', 'Dashboard Wali Murid - Absensi RFID')

@section('page-heading', 'Dashboard Wali Murid')

@section('content')

@php
    $tanggalIndonesia = now()->locale('id')->translatedFormat('l, d F Y');
@endphp

<style>
    .wali-dashboard {
        font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, sans-serif;
        color: #1f2937;
        width: 100%;
    }

    .wali-welcome {
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

    .wali-welcome h2 {
        margin: 0 0 4px;
        color: #850000;
        font-family: inherit;
        font-size: 19px;
        font-weight: 700;
        letter-spacing: -0.015em;
    }

    .wali-welcome p {
        margin: 0;
        color: #6b7280;
        font-family: inherit;
        font-size: 13px;
        font-weight: 500;
    }

    .wali-welcome-date {
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

    .wali-welcome-date svg {
        width: 17px;
        height: 17px;
        flex-shrink: 0;
    }

    /* KARTU */
    .wali-card {
        margin-bottom: 18px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
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
        padding: 20px;
    }

    .wali-data-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .wali-data-item {
        min-width: 0;
        padding: 14px 16px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #fafafa;
    }

    .wali-label {
        margin-bottom: 5px;
        color: #6b7280;
        font-family: inherit;
        font-size: 12px;
        font-weight: 600;
    }

    .wali-value {
        color: #111827;
        font-family: inherit;
        font-size: 14px;
        font-weight: 700;
        overflow-wrap: anywhere;
    }

    .wali-status {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 6px;
        background: #dcfce7;
        color: #15803d;
        font-family: inherit;
        font-size: 11.5px;
        font-weight: 700;
    }

    .wali-status.inactive {
        background: #fee2e2;
        color: #b91c1c;
    }

    .wali-account-info {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .wali-account-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 46px;
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: #fdf2f2;
        border: 1px solid #f5c6cb;
        color: #850000;
    }

    .wali-account-icon svg {
        width: 22px;
        height: 22px;
    }

    .wali-account-text {
        font-family: inherit;
        font-size: 13px;
        line-height: 1.6;
        color: #4b5563;
    }

    .wali-account-text strong {
        color: #850000;
        font-weight: 700;
    }

    .wali-logout {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .wali-logout p {
        margin: 0;
        color: #6b7280;
        font-family: inherit;
        font-size: 13px;
    }

    .wali-logout form {
        margin: 0;
    }

    .wali-logout-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 9px 16px;
        border: none;
        border-radius: 8px;
        background: #850000;
        color: #ffffff;
        font-family: inherit;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.18s ease;
    }

    .wali-logout-button:hover {
        background: #680000;
    }

    @media (max-width: 600px) {
        .wali-welcome {
            padding: 16px;
            flex-direction: column;
            align-items: flex-start;
        }

        .wali-welcome h2 {
            font-size: 17px;
        }

        .wali-welcome-date {
            font-size: 12px;
            padding: 7px 12px;
        }

        .wali-data-grid {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .wali-card-body {
            padding: 16px;
        }
    }
</style>

<div class="wali-dashboard">

    <div class="wali-welcome">
        <div>
            <h2>Selamat Datang, {{ auth()->user()->username }}!</h2>
            <p>Pantau informasi dan kehadiran anak Anda melalui sistem ini.</p>
        </div>

        <div class="wali-welcome-date">
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
            <h3>Informasi Anak</h3>
            <p>Informasi siswa yang terhubung dengan akun wali murid</p>
        </div>

        <div class="wali-card-body">

            <div class="wali-data-grid">

                <div class="wali-data-item">
                    <div class="wali-label">Nama Lengkap</div>
                    <div class="wali-value">
                        {{ $anak->nama_lengkap }}
                    </div>
                </div>

                <div class="wali-data-item">
                    <div class="wali-label">NISN</div>
                    <div class="wali-value">
                        {{ $anak->nisn }}
                    </div>
                </div>

                <div class="wali-data-item">
                    <div class="wali-label">Kelas</div>
                    <div class="wali-value">
                        {{ $anak->kelas->nama_kelas ?? '-' }}
                    </div>
                </div>

                <div class="wali-data-item">
                    <div class="wali-label">Jenis Kelamin</div>
                    <div class="wali-value">
                        {{ $anak->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                    </div>
                </div>

                <div class="wali-data-item">
                    <div class="wali-label">Status Siswa</div>
                    <div>
                        <span class="wali-status {{ $anak->status !== 'aktif' ? 'inactive' : '' }}">
                            {{ ucfirst($anak->status) }}
                        </span>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <section class="wali-card">

        <div class="wali-card-header">
            <h3>Informasi Akun</h3>
            <p>Informasi akun wali murid yang sedang digunakan</p>
        </div>

        <div class="wali-card-body">

            <div class="wali-account-info">

                <div class="wali-account-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>

                <div class="wali-account-text">
                    Username:
                    <strong>{{ auth()->user()->username }}</strong>
                    <br>
                    Akun ini terhubung dengan satu data siswa.
                </div>

            </div>

        </div>
    </section>

    <section class="wali-card">

        <div class="wali-card-header">
            <h3>Keluar dari Sistem</h3>
            <p>Akhiri sesi penggunaan akun wali murid</p>
        </div>

        <div class="wali-card-body">

            <div class="wali-logout">

                <p>
                    Pastikan Anda sudah selesai menggunakan sistem.
                </p>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="wali-logout-button">
                        Keluar Sekarang
                    </button>
                </form>

            </div>

        </div>
    </section>

</div>

@endsection