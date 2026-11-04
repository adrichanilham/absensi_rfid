
@extends('layouts.app')

@section('title', 'Dashboard Wali Murid')

@section('page-heading', 'Dashboard Wali Murid')

@section('content')

<style>
    .wali-dashboard {
        font-family: Georgia, 'Times New Roman', serif;
        color: #292323;
        padding: 4px 2px 20px;
    }

    /* SAMBUTAN */

    .wali-welcome {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
    }

    .wali-welcome h2 {
        margin: 0 0 5px;
        color: #850000;
        font-size: 19px;
        font-weight: bold;
    }

    .wali-welcome p {
        margin: 0;
        color: #777;
        font-size: 12px;
        font-style: italic;
    }

    .wali-welcome-date {
        padding: 8px 12px;
        border-radius: 6px;
        background: #850000;
        color: white;
        font-family: Arial, sans-serif;
        font-size: 11px;
        font-weight: bold;
    }

    /* KARTU */

    .wali-card {
        margin-bottom: 16px;
        border: 1px solid #e8e1e1;
        border-radius: 8px;
        background: #fff;
        overflow: hidden;
    }

    .wali-card-header {
        padding: 12px 15px;
        background: #850000;
        color: white;
    }

    .wali-card-header h3 {
        margin: 0;
        font-size: 15px;
        font-weight: bold;
    }

    .wali-card-header p {
        margin: 4px 0 0;
        color: #f1dada;
        font-family: Arial, sans-serif;
        font-size: 11px;
    }

    .wali-card-body {
        padding: 16px;
    }

    /* INFORMASI ANAK */

    .wali-data-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .wali-data-item {
        min-width: 0;
        padding: 12px;
        border: 1px solid #eee5e5;
        border-radius: 6px;
        background: #fdfafa;
    }

    .wali-label {
        margin-bottom: 6px;
        color: #888;
        font-family: Arial, sans-serif;
        font-size: 11px;
    }

    .wali-value {
        color: #4b2929;
        font-family: Arial, sans-serif;
        font-size: 13px;
        font-weight: bold;
        overflow-wrap: anywhere;
    }

    .wali-status {
        display: inline-block;
        padding: 4px 9px;
        border-radius: 4px;
        background: #e2f4e7;
        color: #18733a;
        font-family: Arial, sans-serif;
        font-size: 10px;
        font-weight: bold;
    }

    /* INFORMASI AKUN */

    .wali-account-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .wali-account-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 42px;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #f5e7e7;
        color: #850000;
        font-family: Arial, sans-serif;
        font-size: 19px;
    }

    .wali-account-text {
        font-family: Arial, sans-serif;
        font-size: 12px;
        line-height: 1.7;
        color: #555;
    }

    .wali-account-text strong {
        color: #850000;
    }

    /* LOGOUT */

    .wali-logout {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .wali-logout p {
        margin: 0;
        color: #777;
        font-family: Arial, sans-serif;
        font-size: 12px;
    }

    .wali-logout-button {
        padding: 8px 14px;
        border: none;
        border-radius: 5px;
        background: #850000;
        color: white;
        font-family: Arial, sans-serif;
        font-size: 11px;
        font-weight: bold;
        cursor: pointer;
        transition: background .2s;
    }

    .wali-logout-button:hover {
        background: #650000;
    }

    /* RESPONSIVE */

    @media (max-width: 600px) {
        .wali-welcome h2 {
            font-size: 17px;
        }

        .wali-data-grid {
            grid-template-columns: 1fr;
            gap: 9px;
        }

        .wali-card-body {
            padding: 12px;
        }

        .wali-welcome-date {
            font-size: 10px;
        }
    }
</style>

<div class="wali-dashboard">

    <!-- SAMBUTAN -->
    <div class="wali-welcome">
        <div>
            <h2>Selamat Datang, {{ auth()->user()->username }}!</h2>
            <p>Pantau informasi dan kehadiran anak Anda melalui sistem ini.</p>
        </div>

        <div class="wali-welcome-date">
            🗓️ {{ now()->translatedFormat('l, d F Y') }}
        </div>
    </div>

    <!-- INFORMASI ANAK -->
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
                        <span class="wali-status">
                            {{ ucfirst($anak->status) }}
                        </span>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- INFORMASI AKUN -->
    <section class="wali-card">

        <div class="wali-card-header">
            <h3>Informasi Akun</h3>
            <p>Informasi akun wali murid yang sedang digunakan</p>
        </div>

        <div class="wali-card-body">

            <div class="wali-account-info">

                <div class="wali-account-icon">
                    👤
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

    <!-- LOGOUT -->
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

                </form>

            </div>

        </div>
    </section>

</div>

@endsection
