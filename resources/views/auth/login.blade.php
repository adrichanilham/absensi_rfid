<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Absensi RFID SDN 37 Sungai Bangek</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @include('layouts.pwa')

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, sans-serif;
            background: #f3f4f6;
            color: #1f2937;
        }

        .login-container {
            width: 100%;
            max-width: 1040px;
            min-height: 490px;
            display: grid;
            grid-template-columns: 52% 48%;
            overflow: hidden;
            border-radius: 16px;
            background: #850000;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
        }

        /* BAGIAN KIRI */
        .login-left {
            background: #850000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 24px;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            padding: 32px 28px;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
        }

        .login-title {
            text-align: center;
            margin-bottom: 22px;
        }

        .login-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            margin-bottom: 12px;
            border-radius: 20px;
            background: #fdf2f2;
            border: 1px solid #f5c6cb;
            color: #850000;
            font-size: 11.5px;
            font-weight: 700;
        }

        .login-title h2 {
            font-family: inherit;
            font-size: 19px;
            line-height: 1.35;
            color: #850000;
            margin: 0;
            font-weight: 800;
            letter-spacing: -0.015em;
        }

        .login-title p {
            margin: 6px 0 0;
            color: #6b7280;
            font-size: 13px;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            color: #374151;
            font-size: 12.5px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .input-wrapper {
            position: relative;
        }

        .form-group input {
            width: 100%;
            height: 42px;
            padding: 9px 13px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #ffffff;
            color: #1f2937;
            font-family: inherit;
            font-size: 13px;
            outline: none;
            transition: border-color 0.18s ease, box-shadow 0.18s ease;
        }

        .form-group input:focus {
            border-color: #850000;
            box-shadow: 0 0 0 3px rgba(133, 0, 0, 0.1);
        }

        .password-input {
            padding-right: 42px !important;
        }

        .toggle-password {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border: none;
            border-radius: 6px;
            background: transparent;
            color: #6b7280;
            cursor: pointer;
            padding: 0;
            transition: color 0.18s ease, background 0.18s ease;
        }

        .toggle-password:hover {
            color: #850000;
            background: #fdf2f2;
        }

        .toggle-password svg {
            width: 18px;
            height: 18px;
        }

        .login-button {
            width: 100%;
            height: 44px;
            margin-top: 8px;
            background: #850000;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.18s ease;
        }

        .login-button:hover {
            background: #680000;
        }

        .rfid-status {
            text-align: center;
            color: #15803d;
            font-weight: 600;
            font-size: 12px;
            margin: 14px 0 0;
        }

        /* NOTIFIKASI */
        .error, .success {
            padding: 11px 13px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 12.5px;
            font-weight: 500;
            line-height: 1.5;
        }

        .error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        /* BAGIAN KANAN */
        .login-right {
            position: relative;
            min-height: 490px;
            background-image:
                linear-gradient(to bottom, rgba(133, 0, 0, 0.18), rgba(45, 0, 0, 0.55)),
                url('/images/sekolah.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            align-items: flex-start;
            padding: 28px;
        }

        .login-right-caption {
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(4px);
            padding: 14px 18px;
            border-radius: 10px;
            border-left: 4px solid #850000;
            max-width: 360px;
        }

        .login-right-caption strong {
            display: block;
            color: #850000;
            font-size: 13.5px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .login-right-caption span {
            display: block;
            color: #4b5563;
            font-size: 12px;
            line-height: 1.45;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            body {
                padding: 14px;
            }

            .login-container {
                grid-template-columns: 1fr;
                max-width: 460px;
                border-radius: 14px;
            }

            .login-left {
                padding: 26px 18px;
            }

            .login-right {
                min-height: 210px;
                order: -1;
                padding: 18px;
            }
        }

        @media (max-width: 400px) {
            .login-card {
                padding: 24px 18px;
            }

            .login-title h2 {
                font-size: 17px;
            }
        }
    </style>
</head>

<body>

<div class="login-container">

    <!-- BAGIAN FORM LOGIN -->
    <div class="login-left">

        <main class="login-card">

            <div class="login-title">
                <span class="login-badge">Absensi Digital</span>
                <h2>
                    SISTEM ABSENSI SEKOLAH<br>
                    SDN 37 SUNGAI BANGEK
                </h2>
                <p>Terintegrasi Dengan Mesin RFID</p>
            </div>

            @if (session('success'))
                <div class="success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="error">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('login.process') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="username">Username / Email</label>

                    <div class="input-wrapper">
                        <input
                            id="username"
                            name="username"
                            type="text"
                            placeholder="Masukkan username atau email"
                            value="{{ old('username') }}"
                            autocomplete="username"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>

                    <div class="input-wrapper">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            class="password-input"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            class="toggle-password"
                            onclick="togglePassword()"
                            aria-label="Tampilkan atau sembunyikan password"
                        >
                            <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="login-button">
                    Masuk ke Sistem
                </button>

            </form>

        </main>

    </div>

    <!-- BAGIAN FOTO SEKOLAH -->
    <div class="login-right">
        <div class="login-right-caption">
            <strong>SDN 37 Sungai Bangek</strong>
            <span>Pencatatan kehadiran siswa secara cepat, akurat, dan terpantau langsung.</span>
        </div>
    </div>

</div>

<script>
    function togglePassword() {
        const password = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');

        if (password.type === 'password') {
            password.type = 'text';
            eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" /><line x1="1" y1="1" x2="23" y2="23" />';
        } else {
            password.type = 'password';
            eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" /><circle cx="12" cy="12" r="3" />';
        }
    }
</script>

</body>
</html>