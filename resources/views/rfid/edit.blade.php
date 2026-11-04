<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kartu RFID</title>

    <style>
        * { box-sizing: border-box; }

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

        h1 { margin-top: 0; }

        .subtitle {
            color: #64748b;
            line-height: 1.6;
        }

        .form-group { margin: 20px 0; }

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
            background: white;
            font-size: 15px;
        }

        .help {
            color: #64748b;
            font-size: 13px;
            line-height: 1.5;
        }

        .error {
            color: #b91c1c;
            font-size: 13px;
            margin-top: 6px;
        }

        .alert {
            padding: 12px;
            background: #fee2e2;
            color: #991b1b;
            border-radius: 7px;
            margin: 16px 0;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 25px;
        }

        .button {
            display: inline-block;
            padding: 11px 16px;
            border: 0;
            border-radius: 7px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }

        .button-secondary { background: #64748b; }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <h1>Edit Kartu RFID</h1>

        <p class="subtitle">
            Perbarui UID, pemilik kartu, atau status kartu RFID.
        </p>

        @if ($errors->any())
            <div class="alert">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('rfid.update', $rfid) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="uid">UID Kartu RFID</label>

                <input
                    type="text"
                    id="uid"
                    name="uid"
                    maxlength="100"
                    value="{{ old('uid', $rfid->uid) }}"
                    required
                >

                @error('uid')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="siswa_id">Pemilik Kartu</label>

                <select id="siswa_id" name="siswa_id" required>
                    <option value="">-- Pilih siswa --</option>

                    @foreach ($siswa as $item)
                        <option
                            value="{{ $item->id }}"
                            @selected(old('siswa_id', $rfid->siswa_id) == $item->id)
                        >
                            {{ $item->nama_lengkap }}
                            — NISN: {{ $item->nisn }}
                            — {{ $item->kelas?->nama_kelas ?? 'Belum ada kelas' }}
                        </option>
                    @endforeach
                </select>

                <p class="help">
                    Pilih siswa aktif yang belum memiliki kartu lain.
                </p>

                @error('siswa_id')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="aktif">Status Kartu</label>

                <select id="aktif" name="aktif" required>
                    <option value="1"
                        @selected((string) old('aktif', (int) $rfid->aktif) === '1')>
                        Aktif
                    </option>

                    <option value="0"
                        @selected((string) old('aktif', (int) $rfid->aktif) === '0')>
                        Nonaktif
                    </option>
                </select>

                @error('aktif')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="actions">
                <button type="submit" class="button">
                    Simpan Perubahan
                </button>

                <a href="{{ route('rfid.index') }}"
                   class="button button-secondary">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
