
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Absensi RFID')</title>

    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --maroon: #850000;
            --maroon-dark: #700000;
            --pink-menu: #bd9292;
            --pink-hover: #d0a5a5;
            --sidebar-width: 235px;
            --text: #292929;
            --muted: #777;
            --border: #e5e5e5;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: var(--text);
            background: #f5f5f5;
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
            background: var(--maroon);
            color: white;
            padding: 12px 12px 14px;
            display: flex;
            flex-direction: column;
            transition: margin-left .3s ease;
            z-index: 100;
            flex-shrink: 0;
        }

        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,.3);
            border-radius: 10px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 2px 15px;
            border-bottom: 1px solid rgba(255,255,255,.25);
            margin-bottom: 12px;
        }

        .brand-icon {
            font-size: 30px;
            line-height: 1;
        }

        .brand h2 {
            font-family: Georgia, serif;
            font-size: 14px;
            line-height: 1.3;
            margin: 0;
            font-weight: bold;
        }

        .nav-label {
            color: #e5baba;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .6px;
            padding: 0 10px;
            margin: 10px 0 6px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 9px;
            color: #f7e3e3;
            text-decoration: none;
            padding: 8px 11px;
            border-radius: 8px;
            margin: 3px 0;
            font-family: Georgia, serif;
            font-size: 13px;
            font-weight: bold;
            background: var(--pink-menu);
            transition: .2s;
        }

        .nav-icon {
            width: 20px;
            min-width: 20px;
            text-align: center;
            font-size: 15px;
        }

        .nav-link:hover {
            background: var(--pink-hover);
            color: white;
            transform: translateX(2px);
        }

        .nav-link.active {
            background: #898383;
            color: white;
        }

        /* ================= PANEL RFID ================= */

        .sidebar-bottom {
            margin-top: auto;
            padding-top: 15px;
            display: flex;
            justify-content: center;
        }

        .rfid-panel {
            width: 125px;
            padding: 11px 9px;
            background: #a14b4b;
            border-radius: 8px;
            text-align: center;
            color: white;
        }

        .rfid-panel h3 {
            font-family: Georgia, serif;
            font-size: 13px;
            margin: 0 0 8px;
        }

        .rfid-panel a {
            display: block;
            color: #fff;
            text-decoration: none;
            font-size: 11px;
            margin-top: 6px;
        }

        .rfid-panel a:hover {
            text-decoration: underline;
        }

        /* ================= AREA KONTEN ================= */

        .main-area {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        /* ================= HEADER ================= */

        .topbar {
            min-height: 78px;
            background: white;
            border-bottom: 1px solid #ddd;
            padding: 12px 22px;
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
            gap: 18px;
            min-width: 0;
        }

        .menu-toggle {
            border: none;
            background: transparent;
            color: #111;
            font-size: 28px;
            line-height: 1;
            cursor: pointer;
            padding: 3px;
            flex-shrink: 0;
        }

        .menu-toggle:hover {
            color: var(--maroon);
        }

        .topbar h1 {
            font-family: Georgia, serif;
            font-size: 21px;
            margin: 0;
            color: #252525;
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
            width: 42px;
            height: 42px;
            min-width: 42px;
            border-radius: 50%;
            background: var(--maroon);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 17px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .user-detail {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            min-width: 85px;
            text-align: left;
            line-height: 1.4;
        }

        .user-detail p {
            margin: 0;
            color: #292929;
            font-size: 13px;
            font-weight: bold;
        }

        .user-detail small {
            display: block;
            margin-top: 2px;
            color: var(--muted);
            font-size: 11px;
        }

        .user-chevron {
            display: none;
        }

        .user-info form {
            margin: 0;
        }

        .logout-button {
            border: 1px solid #e5dada;
            background: white;
            color: var(--maroon);
            padding: 9px 15px;
            border-radius: 7px;
            cursor: pointer;
            font-weight: bold;
            font-size: 12px;
            white-space: nowrap;
            transition: .2s;
        }

        .logout-button:hover {
            background: #fff1f1;
            border-color: var(--maroon);
        }

        /* ================= KONTEN HALAMAN ================= */

        .content {
            flex: 1;
            width: 100%;
            padding: 24px;
            max-width: 1600px;
            margin: 0 auto;
            background: #fff;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 13px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }

        footer {
            text-align: center;
            color: #888;
            font-size: 11px;
            padding: 13px;
            background: white;
        }

        /* ================= SIDEBAR COLLAPSE ================= */

        .app.sidebar-hidden .sidebar {
            margin-left: calc(var(--sidebar-width) * -1);
        }

        /* ================= RESPONSIVE ================= */

        @media (max-width: 800px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                min-height: 100vh;
                overflow-y: auto;
                margin-left: 0;
            }

            .app .main-area {
                width: 100%;
            }

            .app.sidebar-hidden .sidebar {
                margin-left: calc(var(--sidebar-width) * -1);
            }

            .topbar {
                min-height: 70px;
                padding: 12px 15px;
            }

            .topbar-left {
                gap: 10px;
            }

            .topbar h1 {
                font-size: 18px;
            }

            .content {
                padding: 16px 13px;
            }

            .user-avatar {
                width: 36px;
                height: 36px;
                min-width: 36px;
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .topbar {
                gap: 8px;
                padding: 10px;
            }

            .topbar-left {
                gap: 7px;
            }

            .topbar h1 {
                font-size: 15px;
            }

            .menu-toggle {
                font-size: 23px;
            }

            .user-info {
                gap: 7px;
            }

            .user-avatar {
                width: 32px;
                height: 32px;
                min-width: 32px;
                font-size: 12px;
            }

            .user-detail {
                min-width: 0;
            }

            .user-detail p {
                font-size: 10px;
            }

            .user-detail small {
                font-size: 9px;
            }

            .logout-button {
                padding: 7px 9px;
                font-size: 10px;
            }

            .content {
                padding: 14px 11px;
            }
        }

        /* ================= LAYAR PENDEK ================= */

        @media (min-width: 801px) and (max-height: 750px) {
            .sidebar {
                padding-top: 9px;
                padding-bottom: 9px;
            }

            .brand {
                padding-bottom: 10px;
                margin-bottom: 8px;
            }

            .nav-label {
                margin: 7px 0 4px;
            }

            .nav-link {
                padding: 6px 10px;
                margin: 2px 0;
                font-size: 12px;
            }

            .sidebar-bottom {
                padding-top: 8px;
            }

            .rfid-panel {
                padding: 8px;
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

                <div class="nav-label">Perangkat & Absensi</div>

                <a class="nav-link {{ request()->routeIs('rfid.*') ? 'active' : '' }}"
                   href="{{ route('rfid.index') }}">
                    <span class="nav-icon">▧</span>
                    Data RFID
                </a>

                <a class="nav-link {{ request()->routeIs('absensi.scan') ? 'active' : '' }}"
                   href="{{ route('absensi.scan') }}">
                    <span class="nav-icon">▥</span>
                    Scan RFID
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
                <div class="rfid-panel">
                    <h3>RFID</h3>

                    <a href="{{ route('rfid.index') }}">
                        ▸ Data Perangkat
                    </a>

                    <a href="{{ route('absensi.scan') }}">
                        ▸ Scan RFID
                    </a>
                </div>
            </div>
        @endif

    </aside>

    <div class="main-area">

        <header class="topbar">

            <div class="topbar-left">

                <button
                    type="button"
                    class="menu-toggle"
                    id="menuToggle"
                    aria-label="Buka atau tutup menu"
                    aria-expanded="true">
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
            Sistem Informasi Absensi Siswa Berbasis RFID
            &copy; {{ date('Y') }} - SDN 37 Sungai Bangek
        </footer>

    </div>

</div>

<script>
    const app = document.getElementById('app');
    const menuToggle = document.getElementById('menuToggle');

    menuToggle.addEventListener('click', function () {
        app.classList.toggle('sidebar-hidden');

        const isOpen = !app.classList.contains('sidebar-hidden');

        menuToggle.setAttribute('aria-expanded', isOpen);
    });
</script>

@stack('scripts')

</body>
</html>
