<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pengajuan Izin - Absensi RFID</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f3f6fb;
            color: #1f2937;
        }

        .navbar {
            background: #1d4ed8;
            color: white;
            padding: 18px 6%;
        }

        .navbar h2 {
            font-size: 20px;
        }

        .container {
            max-width: 720px;
            margin: 35px auto;
            padding: 0 20px;
        }

        .back-link {
            display: inline-block;
            color: #2563eb;
            text-decoration: none;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .card {
            background: white;
            padding: 28px;
            border-radius: 12px;
            box-shadow: 0 3px 12px #0000000a;
        }

        h1 {
            font-size: 25px;
            margin-bottom: 8px;
        }

        .description {
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .student-info {
            background: #eff6ff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .student-info p {
            margin: 6px 0;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-family: inherit;
            font-size: 14px;
            background: white;
        }

        input:focus, select:focus, textarea:focus {
            outline: 2px solid #bfdbfe;
            border-color: #2563eb;
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        .hint {
            color: #64748b;
            font-size: 12px;
            margin-top: 6px;
        }

        .error-box {
            background: #fee2e2;
            color: #991b1b;
            border-radius: 8px;
            padding: 14px 18px;
            margin-bottom: 20px;
        }

        .error-box ul {
            padding-left: 20px;
            margin-top: 8px;
        }

        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 25px;
        }

        .button {
            display: inline-block;
            border: none;
            border-radius: 7px;
            padding: 12px 18px;
            font-size: 14px;
            text-decoration: none;
            cursor: pointer;
        }

        .primary {
            background: #2563eb;
            color: white;
        }

        .primary:hover {
            background: #1d4ed8;
        }

        .secondary {
            background: #e2e8f0;
            color: #334155;
        }

        footer {
            text-align: center;
            color: #64748b;
            font-size: 13px;
            padding: 25px 15px;
        }

        @media (max-width: 600px) {
            .card {
                padding: 20px;
            }

            h1 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <h2>Absensi RFID</h2>
    </nav>

    <main class="container">
        <a href="{{ route('wali.pengajuan.index') }}" class="back-link">
            &larr; Kembali ke Daftar Pengajuan
        </a>

        <section class="card">
            <h1>Form Pengajuan Izin/Sakit</h1>

            <p class="description">
                Isi formulir berikut dengan benar. Pengajuan akan dikirim
                kepada operator untuk diverifikasi.
            </p>

            <div class="student-info">
                <p><strong>Nama Siswa:</strong> {{ $anak->nama_lengkap }}</p>
                <p><strong>NISN:</strong> {{ $anak->NISN }}</p>
                <p><strong>Kelas:</strong> {{ $anak->kelas->nama_kelas ?? '-' }}</p>
            </div>

            @if ($errors->any())
                <div class="error-box">
                    <strong>Pengajuan belum dapat dikirim.</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                action="{{ route('wali.pengajuan.store') }}"
                method="POST"
                enctype="multipart/form-data"
            >
                @csrf

                <div class="form-group">
                    <label for="jenis">Jenis Pengajuan</label>

                    <select id="jenis" name="jenis" required>
                        <option value="">-- Pilih Jenis --</option>
                        <option value="izin" @selected(old('jenis') === 'izin')>
                            Izin
                        </option>
                        <option value="sakit" @selected(old('jenis') === 'sakit')>
                            Sakit
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tanggal_mulai">Tanggal Mulai</label>

                    <input
                        type="date"
                        id="tanggal_mulai"
                        name="tanggal_mulai"
                        value="{{ old('tanggal_mulai') }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="tanggal_selesai">Tanggal Selesai</label>

                    <input
                        type="date"
                        id="tanggal_selesai"
                        name="tanggal_selesai"
                        value="{{ old('tanggal_selesai') }}"
                        required
                    >
                    <p class="hint">
                        Tanggal selesai tidak boleh lebih awal daripada tanggal mulai.
                    </p>
                </div>

                <div class="form-group">
                    <label for="alasan">Alasan Izin/Sakit</label>

                    <textarea
                        id="alasan"
                        name="alasan"
                        maxlength="1000"
                        placeholder="Jelaskan alasan pengajuan..."
                        required
                    >{{ old('alasan') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="dokumen">Dokumen Pendukung (Opsional)</label>

                    <input
                        type="file"
                        id="dokumen"
                        name="dokumen"
                        accept=".pdf,.jpg,.jpeg,.png"
                    >

                    <p class="hint">
                        Format yang diperbolehkan: PDF, JPG, JPEG, PNG. Maksimal 2 MB.
                    </p>
                </div>

                <div class="actions">
                    <button type="submit" class="button primary">
                        Kirim Pengajuan
                    </button>

                    <a
                        href="{{ route('wali.pengajuan.index') }}"
                        class="button secondary"
                    >
                        Batal
                    </a>
                </div>
            </form>
        </section>
    </main>

    <footer>
        Sistem Informasi Absensi Siswa Berbasis RFID
    </footer>

</body>
</html>
