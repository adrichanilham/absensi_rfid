
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Absensi RFID SDN 37 Sungai Bangek</title>

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
            font-family: Arial, sans-serif;
            background: #f1f1f1;
            color: #1e293b;
        }

        .login-container {
            width: 100%;
            max-width: 1100px;
            min-height: 460px;
            display: grid;
            grid-template-columns: 53% 47%;
            overflow: hidden;
            border-radius: 5px;
            background: #800000;
        }

        /* BAGIAN KIRI */

        .login-left {
            background: #800000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 25px;
        }

        .login-card {
            width: 100%;
            max-width: 470px;
            background: white;
            padding: 28px 22px;
            border-radius: 15px;
        }

        .login-title {
            text-align: center;
            margin-bottom: 8px;
        }

        .login-title h2 {
            font-family: Georgia, serif;
            font-size: 19px;
            line-height: 1.2;
            color: #800000;
            margin: 0;
            font-weight: bold;
        }

        .login-title p {
            margin: 5px 0 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 13px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .input-wrapper {
            position: relative;
        }

        .form-group input {
            width: 100%;
            height: 39px;
            padding: 10px;
            border: 1px solid #999;
            border-radius: 8px;
            font-size: 13px;
            outline: none;
        }

        .form-group input:focus {
            border-color: #800000;
            box-shadow: 0 0 0 2px #80000020;
        }

        .password-input {
            padding-right: 42px !important;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            cursor: pointer;
            padding: 4px;
            font-size: 17px;
        }

        .login-button {
            width: 100%;
            height: 49px;
            margin-top: 5px;
            background: #800000;
            color: white;
            border: none;
            border-radius: 8px;
            font-family: Georgia, serif;
            font-size: 19px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
        }

        .login-button:hover {
            background: #600000;
        }

        .rfid-status {
            text-align: center;
            color: #00b900;
            font-weight: bold;
            font-size: 12px;
            margin: 10px 0 0;
        }

        /* PESAN ERROR */

        .error, .success {
            padding: 10px;
            border-radius: 7px;
            margin-bottom: 12px;
            font-size: 12px;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
        }

        .success {
            background: #dcfce7;
            color: #166534;
        }

        /* BAGIAN KANAN */

        .login-right {
            position: relative;
            min-height: 460px;
            background-image:
                url('/images/sekolah.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 25px 15px;
        }

        .login-right h1 {
            margin: 0;
            text-align: center;
            color: #e00000;
            font-family: Georgia, serif;
            font-size: 25px;
            line-height: 1.4;
            font-weight: bold;
            text-shadow: 1px 2px 2px white;
        }

        /* RESPONSIVE */

        @media (max-width: 700px) {
            body {
                padding: 12px;
            }

            .login-container {
                grid-template-columns: 1fr;
                max-width: 480px;
            }

            .login-left {
                padding: 30px 18px;
            }

            .login-right {
                min-height: 220px;
                order: -1;
            }

            .login-right h1 {
                font-size: 21px;
            }
        }

        @media (max-width: 380px) {
            .login-card {
                padding: 22px 16px;
            }

            .login-title h2 {
                font-size: 16px;
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
                    <label for="username">Username/Email</label>

                    <div class="input-wrapper">
                        <input
                            id="username"
                            name="username"
                            type="text"
                            placeholder="masukkan username atau email"
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
                            placeholder="masukkan password"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            class="toggle-password"
                            onclick="togglePassword()"
                            aria-label="Tampilkan password"
                        >
                            👁
                        </button>
                    </div>
                </div>

                <button type="submit" class="login-button">
                    Login
                </button>

                <p class="rfid-status">
                    
                </p>

            </form>

        </main>

    </div>

    <!-- BAGIAN FOTO SEKOLAH -->
    <div class="login-right">

    

    </div>

</div>

<script>
    function togglePassword() {
        const password = document.getElementById('password');

        password.type = password.type === 'password'
            ? 'text'
            : 'password';
    }
</script>

</body>
</html>
