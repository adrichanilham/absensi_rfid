<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Absensi RFID')</title>

    @include('layouts.pwa')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --font-main: 'Plus Jakarta Sans', 'Segoe UI', system-ui, -apple-system, sans-serif;
            --maroon: #850000;
            --maroon-dark: #680000;
            --sidebar-width: 245px;
            --bg-page: #f4f6f8;
            --text: #1f2937;
            --muted: #6b7280;
            --border: #e5e7eb;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: var(--font-main);
            color: var(--text);
            background: var(--bg-page);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        h1, h2, h3, h4,
        input, select, textarea, button,
        .rfid-page-title, .rfid-card-banner h2,
        .izin-page-title, .izin-card-banner h2 {
            font-family: var(--font-main) !important;
        }

        .app {
            display: flex;
            min-height: 100vh;
        }

        /* ================= SIDEBAR ================= */

        .sidebar {
            width: var(--sidebar-width);
            min-width: var(--sidebar-width);
            height: 100vh;
            position: sticky;
            top: 0;
            align-self: flex-start;
            overflow-y: auto;
            overflow-x: hidden;
            background: var(--maroon, #850000);
            color: #ffffff;
            padding: 16px 14px;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease;
            z-index: 100;
            flex-shrink: 0;
            box-shadow: 2px 0 12px rgba(0, 0, 0, 0.08);
        }

        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.25);
            border-radius: 10px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 4px 6px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.18);
            margin-bottom: 14px;
        }

        .brand-icon {
            font-size: 24px;
            line-height: 1;
            background: rgba(255, 255, 255, 0.12);
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #ffffff;
        }

        .brand h2 {
            font-size: 14px;
            line-height: 1.35;
            margin: 0;
            font-weight: 700;
            letter-spacing: -0.01em;
            color: #ffffff;
        }

        .nav-label {
            color: rgba(255, 255, 255, 0.65);
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 0 10px;
            margin: 14px 0 6px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fce8e8;
            text-decoration: none;
            padding: 9px 12px;
            border-radius: 8px;
            margin: 3px 0;
            font-size: 13px;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.08);
            transition: all 0.18s ease;
        }

        .nav-icon {
            width: 20px;
            min-width: 20px;
            text-align: center;
            font-size: 15px;
            opacity: 0.9;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.18);
            color: #ffffff;
            transform: translateX(2px);
        }

        .nav-link.active {
            background: #ffffff;
            color: var(--maroon, #850000);
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.16);
        }

        .nav-link.active .nav-icon {
            opacity: 1;
        }

        /* ================= TOMBOL SCAN RFID ================= */

        .sidebar-bottom {
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px solid rgba(255, 255, 255, 0.14);
        }

        .scan-rfid-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            width: 100%;
            padding: 10px 14px;
            background: #ffffff;
            color: var(--maroon, #850000);
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            border-radius: 8px;
            transition: background 0.18s ease, transform 0.15s ease;
        }

        .scan-rfid-btn svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .scan-rfid-btn:hover {
            background: #fdf2f2;
        }

        .scan-rfid-btn:active {
            transform: scale(0.98);
        }

        /* ================= BACKDROP MOBILE ================= */

        .sidebar-backdrop {
            display: none;
        }

        /* ================= AREA KONTEN ================= */

        .main-area {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        /* ================= HEADER / TOPBAR ================= */

        .topbar {
            min-height: 70px;
            background: #ffffff;
            border-bottom: 1px solid var(--border);
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .menu-toggle {
            border: 1px solid transparent;
            background: transparent;
            color: #374151;
            font-size: 22px;
            line-height: 1;
            cursor: pointer;
            width: 38px;
            height: 38px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: background 0.15s, color 0.15s;
        }

        .menu-toggle:hover {
            background: #fdf2f2;
            color: var(--maroon, #850000);
        }

        .topbar h1 {
            font-size: 19px;
            font-weight: 700;
            letter-spacing: -0.015em;
            margin: 0;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ================= INFORMASI AKUN ================= */

        .user-info {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            flex-shrink: 0;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            min-width: 38px;
            border-radius: 50%;
            background: var(--maroon, #850000);
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            box-shadow: 0 2px 6px rgba(133, 0, 0, 0.2);
        }

        .user-detail {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            min-width: 80px;
            text-align: left;
            line-height: 1.3;
        }

        .user-detail p {
            margin: 0;
            color: #1f2937;
            font-size: 13px;
            font-weight: 700;
        }

        .user-detail small {
            display: block;
            margin-top: 1px;
            color: var(--muted);
            font-size: 11.5px;
            font-weight: 500;
        }

        .user-info form {
            margin: 0;
        }

        .logout-button {
            border: 1px solid #e5d5d5;
            background: #ffffff;
            color: var(--maroon, #850000);
            padding: 8px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 12.5px;
            white-space: nowrap;
            transition: all 0.15s ease;
        }

        .logout-button:hover {
            background: #fef2f2;
            border-color: var(--maroon, #850000);
        }

        /* ================= KONTEN HALAMAN ================= */

        .content {
            flex: 1;
            width: 100%;
            padding: 26px 24px;
            max-width: 1560px;
            margin: 0 auto;
            background: transparent;
        }

        .alert {
            padding: 13px 16px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 13px;
            font-weight: 500;
            line-height: 1.5;
        }

        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-left: 4px solid #16a34a;
            color: #166534;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-left: 4px solid #dc2626;
            color: #991b1b;
        }

        .alert ul {
            margin: 6px 0 0;
            padding-left: 18px;
        }

        footer {
            text-align: center;
            color: var(--muted);
            font-size: 12px;
            padding: 16px;
            background: #ffffff;
            border-top: 1px solid var(--border);
        }

        /* ================= LAYAR TETAP & SCROLL PROPORSIONAL DESKTOP ================= */

        @media (min-width: 801px) {
            html,
            body {
                height: 100%;
                overflow: hidden;
            }

            .app {
                height: 100vh;
                overflow: hidden;
            }

            .app.sidebar-hidden .sidebar {
                margin-left: calc(var(--sidebar-width) * -1);
            }

            .main-area {
                height: 100vh;
                overflow: hidden;
            }

            .topbar {
                min-height: 58px;
                padding: 8px 22px;
                flex-shrink: 0;
            }

            /* Area konten utama bebas scroll untuk Laporan, Dashboard, dan Form */
            .content {
                flex: 1;
                min-height: 0;
                overflow-y: auto;
                padding: 16px 22px;
                display: block;
            }

            footer {
                padding: 10px 16px;
                font-size: 11.5px;
                flex-shrink: 0;
            }

            /* Halaman daftar tabel (Siswa, Absensi, Kelas, RFID, Manajemen User) */
            .content .student-page,
            .content .attendance-page,
            .content .class-page,
            .content .rfid-page,
            .content .users-page {
                height: 100%;
                min-height: 460px;
                display: flex;
                flex-direction: column;
            }

            .content .student-heading,
            .content .attendance-heading,
            .content .class-heading,
            .content .rfid-heading,
            .content .users-heading {
                padding: 11px 18px;
                margin-bottom: 12px;
                flex-shrink: 0;
            }

            .content .filter-panel,
            .content [class$="-filter"] {
                padding: 12px 16px;
                margin-bottom: 12px;
                flex-shrink: 0;
            }

            .content .filter-title {
                margin-bottom: 8px;
                font-size: 14px;
            }

            /* Panel kartu tabel mengisi sisa tinggi layar */
            .content .student-table-panel,
            .content .attendance-table-panel,
            .content .class-table-panel,
            .content .rfid-table-panel,
            .content .users-card {
                flex: 1;
                min-height: 0;
                display: flex;
                flex-direction: column;
                margin-bottom: 0;
            }

            .content .student-table-header,
            .content .attendance-table-header,
            .content .class-table-header,
            .content .rfid-table-header,
            .content .users-card-header {
                padding: 10px 18px;
                flex-shrink: 0;
            }

            /* Area isi tabel bisa di-scroll internal jika data melebihi tinggi layar */
            .content .student-table-wrapper,
            .content .attendance-table-wrap,
            .content .class-table-wrapper,
            .content .rfid-table-wrapper,
            .content .users-table-wrap {
                flex: 1;
                min-height: 200px;
                overflow: auto;
            }

            .content .student-table thead th,
            .content .attendance-table thead th,
            .content .class-table thead th,
            .content .rfid-table thead th,
            .content .users-table thead th {
                position: sticky;
                top: 0;
                z-index: 5;
                padding: 8px 12px;
                font-size: 11.5px;
                white-space: nowrap;
            }

            .content .student-table tbody td,
            .content .attendance-table tbody td,
            .content .class-table tbody td,
            .content .rfid-table tbody td,
            .content .users-table tbody td {
                padding: 7px 12px;
                font-size: 12.5px;
                white-space: nowrap;
            }

            .content .student-table tbody td a,
            .content .student-table tbody td button,
            .content .attendance-table tbody td a,
            .content .attendance-table tbody td button,
            .content .class-table tbody td a,
            .content .class-table tbody td button,
            .content .rfid-table tbody td a,
            .content .rfid-table tbody td button,
            .content .users-table tbody td a,
            .content .users-table tbody td button {
                padding: 4px 10px;
                font-size: 11.5px;
            }

            .content .student-pagination,
            .content .attendance-pagination,
            .content .class-pagination,
            .content .rfid-pagination,
            .content .users-pagination {
                padding: 8px 16px;
                flex-shrink: 0;
            }
        }

        /* ================= RESPONSIVE HP & TABLET ================= */

        @media (max-width: 800px) {
            .sidebar {
                position: fixed;
                top: 64px;
                left: 0;
                height: calc(100dvh - 64px);
                margin-left: calc(var(--sidebar-width) * -1);
            }

            .app.sidebar-open .sidebar {
                margin-left: 0;
            }

            .sidebar-backdrop {
                position: fixed;
                inset: 64px 0 0 0;
                background: rgba(17, 24, 39, 0.45);
                z-index: 90;
            }

            .app.sidebar-open .sidebar-backdrop {
                display: block;
            }

            .app .main-area {
                width: 100%;
            }

            .topbar {
                min-height: 64px;
                padding: 10px 16px;
                z-index: 110;
            }

            .topbar-left {
                gap: 10px;
            }

            .topbar h1 {
                font-size: 17px;
            }

            .content {
                padding: 18px 14px;
            }

            .user-avatar {
                width: 34px;
                height: 34px;
                min-width: 34px;
                font-size: 13px;
            }
        }

        @media (max-width: 480px) {
            .topbar {
                gap: 8px;
                padding: 10px 12px;
            }

            .topbar-left {
                gap: 6px;
            }

            .topbar h1 {
                font-size: 15px;
            }

            .menu-toggle {
                width: 34px;
                height: 34px;
                font-size: 20px;
            }

            .user-info {
                gap: 8px;
            }

            .user-avatar {
                width: 30px;
                height: 30px;
                min-width: 30px;
                font-size: 12px;
            }

            .user-detail {
                min-width: 0;
            }

            .user-detail p {
                font-size: 11px;
            }

            .user-detail small {
                font-size: 10px;
            }

            .logout-button {
                padding: 6px 10px;
                font-size: 11px;
            }

            .content {
                padding: 14px 12px;
            }
        }

        /* ================= LAYAR PENDEK ================= */

        @media (min-width: 801px) and (max-height: 750px) {
            .sidebar {
                padding-top: 10px;
                padding-bottom: 10px;
            }

            .brand {
                padding-bottom: 10px;
                margin-bottom: 8px;
            }

            .nav-label {
                margin: 8px 0 4px;
            }

            .nav-link {
                padding: 7px 10px;
                margin: 2px 0;
                font-size: 12.5px;
            }

            .sidebar-bottom {
                padding-top: 10px;
            }

            .scan-rfid-btn {
                padding: 9px 12px;
                font-size: 12.5px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

@php
    $user = auth()->user();
    $role = $user->role;
@endphp

<div class="app" id="app">

    <aside class="sidebar">

        <div class="brand">
            <span class="brand-icon">🏛</span>
            <h2>Sistem Absensi Sekolah</h2>
        </div>

        <div class="nav-label">Menu Utama</div>

        @if ($role === 'wali_murid')

            <a class="nav-link {{ request()->routeIs('wali.dashboard') ? 'active' : '' }}"
               href="{{ route('wali.dashboard') }}">
                <span class="nav-icon">▦</span>
                Dashboard
            </a>

            <a class="nav-link {{ request()->routeIs('wali.absensi.*') ? 'active' : '' }}"
               href="{{ route('wali.absensi.index') }}">
                <span class="nav-icon">▤</span>
                Riwayat Absensi Anak
            </a>

            <a class="nav-link {{ request()->routeIs('wali.pengajuan.*') ? 'active' : '' }}"
               href="{{ route('wali.pengajuan.index') }}">
                <span class="nav-icon">✉</span>
                Pengajuan Izin / Sakit
            </a>

        @else

            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               href="{{ route('dashboard') }}">
                <span class="nav-icon">▦</span>
                Dashboard
            </a>

            <a class="nav-link {{ request()->routeIs('absensi.index', 'absensi.edit', 'absensi.update') ? 'active' : '' }}"
               href="{{ route('absensi.index') }}">
                <span class="nav-icon">▤</span>
                Data Absensi
            </a>

            <a class="nav-link {{ request()->routeIs('siswa.*') ? 'active' : '' }}"
               href="{{ route('siswa.index') }}">
                <span class="nav-icon">♙</span>
                Data Siswa
            </a>

            <a class="nav-link {{ request()->routeIs('kelas.*') ? 'active' : '' }}"
               href="{{ route('kelas.index') }}">
                <span class="nav-icon">▣</span>
                Data Kelas
            </a>

            @if ($role === 'operator')
                <a class="nav-link {{ request()->routeIs('rfid.*') ? 'active' : '' }}"
                   href="{{ route('rfid.index') }}">
                    <span class="nav-icon">▧</span>
                    Data RFID
                </a>
            @endif

            <a class="nav-link {{ request()->routeIs('laporan.absensi') ? 'active' : '' }}"
               href="{{ route('laporan.absensi') }}">
                <span class="nav-icon">▤</span>
                Laporan
            </a>

            @if ($role === 'operator')

                <div class="nav-label">Manajemen</div>

                <a class="nav-link {{ request()->routeIs('operator.pengajuan.*') ? 'active' : '' }}"
                   href="{{ route('operator.pengajuan.index') }}">
                    <span class="nav-icon">✉</span>
                    Ket. Sakit / Izin
                </a>

                <a class="nav-link {{ request()->routeIs('operator.users.*') ? 'active' : '' }}"
                   href="{{ route('operator.users.index') }}">
                    <span class="nav-icon">♙</span>
                    Manajemen User
                </a>

            @endif

        @endif

        @if ($role === 'operator')
            <div class="sidebar-bottom">
                <a href="{{ route('absensi.scan') }}" class="scan-rfid-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="5" width="14" height="14" rx="2.5" />
                        <line x1="2" y1="10" x2="16" y2="10" />
                        <path d="M19 9.5a4 4 0 0 1 0 5" />
                        <path d="M21.5 7a7.5 7.5 0 0 1 0 10" />
                    </svg>
                    <span>Scan RFID</span>
                </a>
            </div>
        @endif

    </aside>

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="main-area">

        <header class="topbar">

            <div class="topbar-left">

                <button
                    type="button"
                    class="menu-toggle"
                    id="menuToggle"
                    aria-label="Buka atau tutup menu"
                    aria-expanded="false">
                    ☰
                </button>

                <h1>@yield('page-heading', 'Sistem Absensi RFID')</h1>

            </div>

            <div class="user-info">

                <div class="user-avatar">
                    {{ strtoupper(substr($user->username, 0, 1)) }}
                </div>

                <div class="user-detail">
                    <p>{{ $user->username }}</p>
                    <small>{{ ucfirst(str_replace('_', ' ', $role)) }}</small>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf

                    <button class="logout-button" type="submit">
                        Keluar
                    </button>
                </form>

            </div>

        </header>

        <main class="content">

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    <strong>Periksa kembali input berikut:</strong>

                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')

        </main>

        <footer>
            Sistem Informasi Absensi Siswa Berbasis RFID &copy; {{ date('Y') }} — SDN 37 Sungai Bangek
        </footer>

    </div>

</div>

<script>
    const app = document.getElementById('app');
    const menuToggle = document.getElementById('menuToggle');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');

    function isMobileView() {
        return window.innerWidth <= 800;
    }

    function updateAriaExpanded() {
        const isOpen = isMobileView()
            ? app.classList.contains('sidebar-open')
            : !app.classList.contains('sidebar-hidden');

        menuToggle.setAttribute('aria-expanded', isOpen);
    }

    menuToggle.addEventListener('click', function () {
        if (isMobileView()) {
            app.classList.toggle('sidebar-open');
        } else {
            app.classList.toggle('sidebar-hidden');
        }
        updateAriaExpanded();
    });

    if (sidebarBackdrop) {
        sidebarBackdrop.addEventListener('click', function () {
            app.classList.remove('sidebar-open');
            updateAriaExpanded();
        });
    }

    document.querySelectorAll('.sidebar .nav-link').forEach(function (link) {
        link.addEventListener('click', function () {
            if (isMobileView()) {
                app.classList.remove('sidebar-open');
                updateAriaExpanded();
            }
        });
    });

    updateAriaExpanded();
    window.addEventListener('resize', updateAriaExpanded);
</script>

@stack('scripts')

</body>
</html> 