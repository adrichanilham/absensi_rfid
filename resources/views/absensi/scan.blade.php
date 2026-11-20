
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi RFID - SDN 37 Sungai Bangek</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            color: #333;
        }

        header {
            background: #850000;
            color: white;
            padding: 17px 20px;
            text-align: center;
        }

        header h1 {
            margin: 0 0 5px;
            font-family: Georgia, serif;
            font-size: 21px;
        }

        header p {
            margin: 0;
            color: #f2dede;
            font-size: 11px;
        }

        main {
                width: min(1100px, 94%);
                margin: 30px auto;

                display: grid;
                grid-template-columns: 1.5fr 1fr;
                gap: 24px;
                align-items: start;
        }

        .card {
                background: white;
                padding: 24px;
                border: 1px solid #e8e5e5;
                border-radius: 8px;
                margin-bottom: 0;
                min-width: 0;
        }

        .center {
            text-align: center;
        }

        .icon {
            font-size: 43px;
            margin: 4px 0 10px;
        }

        h2 {
            margin: 0 0 9px;
            color: #850000;
            font-family: Georgia, serif;
            font-size: 17px;
        }

        label {
            display: block;
            font-size: 11px;
            font-weight: bold;
            text-align: left;
            margin: 17px 0 7px;
            color: #555;
        }

        input {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #dedede;
            border-radius: 5px;
            font-size: 13px;
            outline: none;
            color: #333;
            background: #fff;
        }

        input:focus {
            border-color: #850000;
            box-shadow: 0 0 0 2px #85000012;
        }

        button {
            width: 100%;
            margin-top: 10px;
            padding: 11px;
            border: none;
            border-radius: 5px;
            background: #850000;
            color: white;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #690000;
        }

        button:disabled {
            background: #b98a8a;
            cursor: wait;
        }

        .result {
            border-radius: 6px;
            padding: 14px;
            margin-top: 13px;
            background: #fafafa;
            border: 1px solid #e8e5e5;
            overflow-wrap: anywhere;
            font-size: 12px;
        }

        .result h3 {
            margin: 0 0 9px;
            font-size: 13px;
        }

        .result p {
            margin: 5px 0;
            line-height: 1.5;
        }

        .success {
            background: #f0faf3;
            border-color: #cce8d5;
            color: #267443;
        }

        .error {
            background: #fff5f5;
            border-color: #f0cccc;
            color: #a52b2b;
        }

        .muted {
            color: #888;
            font-size: 10px;
            line-height: 1.6;
        }

        .student-name {
            font-size: 19px;
            font-weight: bold;
            margin: 10px 0;
            color: #168044;
        }

        #tanggal {
            margin: 13px 0 4px;
            color: #850000;
            font-size: 12px;
            font-weight: bold;
        }

        #jam {
            margin: 0;
        }

        @media (max-width: 480px) {
            main {
                margin: 16px auto;
            }

            .card {
                padding: 16px;
            }

            header h1 {
                font-size: 19px;
            }
        }

        /* Tampilan tablet dan HP */
        @media (max-width: 768px) {
            main {
                grid-template-columns: 1fr;
                width: 92%;
                gap: 15px;
            }
        
            .card {
                padding: 18px;
            }
        }
    </style>
</head>

<body>
    <header>
        <h1>ABSENSI RFID</h1>
        <p>SDN 37 Sungai Bangek</p>
    </header>

    <main>
        <section class="card center">
            <div class="icon">📡</div>
            <h2>Scan Kartu Siswa</h2>
            <p class="muted">
                Tempelkan kartu RFID pada reader untuk mencatat kehadiran.
            </p>

            <p id="tanggal"></p>
            <p id="jam" class="muted"></p>

            <form id="scanForm">
                <label for="uid">UID Kartu RFID</label>

                <input
                    type="text"
                    id="uid"
                    name="uid"
                    placeholder="Tempelkan kartu atau masukkan UID"
                    autocomplete="off"
                    autofocus
                    required
                    maxlength="100"
                >

                <button type="submit" id="scanButton">
                    Proses Absensi
                </button>
            </form>

            <p class="muted">
                Pastikan kolom UID aktif saat menggunakan reader USB.
            </p>
        </section>

        <section class="card">
            <h2>Hasil Scan Terakhir</h2>

            <div id="hasil" class="result" aria-live="polite">
                Menunggu kartu dipindai.
            </div>
        </section>
    </main>

    <script>
        const form = document.getElementById('scanForm');
        const uidInput = document.getElementById('uid');
        const scanButton = document.getElementById('scanButton');
        const hasil = document.getElementById('hasil');

        let sedangProses = false;

        function perbaruiWaktu() {
            const sekarang = new Date();

            document.getElementById('tanggal').textContent =
                sekarang.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });

            document.getElementById('jam').textContent =
                sekarang.toLocaleTimeString('id-ID', {
                    timeZone: 'Asia/Jakarta',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                }) + ' WIB';
        }

        perbaruiWaktu();
        setInterval(perbaruiWaktu, 1000);

        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            if (sedangProses) return;

            const uid = uidInput.value.trim();

            if (!uid) {
                uidInput.focus();
                return;
            }

            sedangProses = true;
            scanButton.disabled = true;
            scanButton.textContent = 'Memproses...';

            hasil.className = 'result';
            hasil.textContent = 'Memeriksa kartu RFID...';

            try {
                const response = await fetch('/api/rfid/scan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ uid: uid })
                });

                const data = await response.json();

                hasil.replaceChildren();

                const judul = document.createElement('h3');
                judul.textContent = data.message || 'Hasil scan';

                const detail = document.createElement('div');

                if (data.success && data.siswa) {
                    hasil.className = 'result success';

                    const nama = document.createElement('p');
                    nama.className = 'student-name';
                    nama.textContent = data.siswa.nama || '-';
                    detail.appendChild(nama);

                    const kelas = document.createElement('p');
                    kelas.textContent = 'Kelas: ' + (data.siswa.kelas || '-');
                    detail.appendChild(kelas);

                    const nisn = document.createElement('p');
                    nisn.textContent = 'NISN: ' + (data.siswa.nisn || '-');
                    detail.appendChild(nisn);

                    if (data.absensi) {
                        const status = document.createElement('p');
                        status.textContent =
                            'Status: ' + data.absensi.status;
                        detail.appendChild(status);
                    }
                } else {
                    hasil.className = 'result error';
                }

                hasil.append(judul, detail);

            } catch (error) {
                hasil.className = 'result error';
                hasil.textContent =
                    'Tidak dapat menghubungi server. Pastikan Laravel berjalan.';
            } finally {
                sedangProses = false;
                scanButton.disabled = false;
                scanButton.textContent = 'Proses Absensi';
                uidInput.value = '';
                uidInput.focus();
            }
        });

        // Kembalikan fokus ke kolom UID jika pengguna mengeklik area lain.
        document.addEventListener('click', function (event) {
            if (!event.target.closest('input, button')) {
                uidInput.focus();
            }
        });
    </script>
</body>
</html>
