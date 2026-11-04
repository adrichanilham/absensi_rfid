<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftarkan Kartu RFID</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 24px;
            font-family: Arial, sans-serif;
            background: #f3f6fb;
            color: #1f2937;
        }

        .container {
            max-width: 650px;
            margin: 30px auto;
        }

        .card {
            background: white;
            padding: 28px;
            border-radius: 12px;
            box-shadow: 0 2px 10px #0000000a;
        }

        h1 {
            margin: 0 0 8px;
            font-size: 25px;
        }

        .subtitle {
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 14px;
        }

        input, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-size: 15px;
            background: white;
        }

        input:focus, select:focus {
            outline: 2px solid #bfdbfe;
            border-color: #2563eb;
        }

        .help {
            color: #64748b;
            font-size: 13px;
            line-height: 1.5;
            margin-top: 7px;
        }

        .error {
            color: #b91c1c;
            font-size: 13px;
            margin-top: 6px;
        }

        .alert {
            padding: 13px;
            border-radius: 7px;
            background: #fee2e2;
            color: #991b1b;
            margin-bottom: 20px;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 26px;
        }

        .button {
            display: inline-block;
            padding: 11px 16px;
            border: none;
            border-radius: 7px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }

        .button:hover {
            background: #1d4ed8;
        }

        .button-secondary {
            background: #64748b;
        }

        .button-secondary:hover {
            background: #475569;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <h1>Daftarkan Kartu RFID</h1>

        <p class="subtitle">
            Masukkan UID kartu dan pilih siswa yang akan menggunakan kartu
            untuk melakukan absensi di SDN 37 Sungai Bangek.
        </p>

        @if ($errors->any())
            <div class="alert">
                <strong>Periksa kembali data berikut:</strong>

                @foreach ($errors->all() as $error)
                    <div style="margin-top: 5px;">
                        {{ $error }}
                    </div>
                @endforeach
            </div>
        @endif

        @if ($siswa->isEmpty())
            <div class="alert">
                Tidak ada siswa aktif yang belum memiliki kartu RFID.
                Tambahkan siswa terlebih dahulu atau periksa kartu yang
                sudah terdaftar.
            </div>

            <div class="actions">
                <a href="{{ route('rfid.index') }}" class="button button-secondary">
                    Kembali ke Daftar RFID
                </a>

                <a href="{{ route('siswa.create') }}" class="button">
                    Tambah Siswa
                </a>
            </div>
        @else
            <form action="{{ route('rfid.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="uid">UID Kartu RFID</label>

                    <input
                        type="text"
                        id="uid"
                        name="uid"
                        value="{{ old('uid') }}"
                        placeholder="Contoh: 04A1B2C3D4"
                        maxlength="100"
                        autocomplete="off"
                        required
                        autofocus
                    >

                    <p class="help">
                        Tempelkan kartu pada reader jika reader dapat
                        mengetik UID ke kolom ini, atau masukkan UID
                        kartu secara manual.
                    </p>

                    @error('uid')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="siswa_id">Pilih Siswa</label>

                    <select id="siswa_id" name="siswa_id" required>
                        <option value="">-- Pilih siswa --</option>

                        @foreach ($siswa as $item)
                            <option
                                value="{{ $item->id }}"
                                @selected(old('siswa_id') == $item->id)
                            >
                                {{ $item->nama_lengkap }}
                                — NISN: {{ $item->nisn }}
                                — {{ $item->kelas?->nama_kelas ?? 'Belum ada kelas' }}
                            </option>
                        @endforeach
                    </select>

                    <p class="help">
                        Hanya siswa aktif yang belum memiliki kartu RFID
                        yang ditampilkan.
                    </p>

                    @error('siswa_id')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="actions">
                    <button type="submit" class="button">
                        Simpan Kartu RFID
                    </button>

                    <a
                        href="{{ route('rfid.index') }}"
                        class="button button-secondary"
                    >
                        Batal
                    </a>
                </div>
            </form>
        @endif
    </div>
</div>

</body>
</html>
