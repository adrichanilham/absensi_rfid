<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi RFID - SDN 37 Sungai Bangek</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
    margin: 0;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
    -webkit-font-smoothing: antialiased;
    background: #f3f4f6;
    color: #1f2937;
}

header h1,
h2 {
    font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, sans-serif;
    letter-spacing: -0.015em;
}

#jam {
    margin: 0;
    color: #111827;
    font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
    font-size: clamp(30px, 4vw, 48px);
    font-weight: 800;
    font-variant-numeric: tabular-nums;
    letter-spacing: 1px;
    line-height: 1.1;
}
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: #f3f4f6;
            color: #1f2937;
        }

        header {
            background: #850000;
            color: #ffffff;
            padding: clamp(16px, 2.5vh, 24px) 20px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        }

        header h1 {
            margin: 0 0 4px;
            font-size: clamp(22px, 2.8vw, 32px);
            letter-spacing: 0.5px;
        }

        header p {
            margin: 0;
            color: #f8d7da;
            font-size: clamp(13px, 1.4vw, 16px);
        }

        main {
            flex: 1;
            width: min(1240px, 94%);
            margin: clamp(20px, 4vh, 40px) auto;
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            gap: clamp(20px, 2.5vw, 32px);
            align-items: stretch;
        }

        .card {
            background: #ffffff;
            padding: clamp(24px, 3vw, 36px);
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 0;
        }

        .result-card {
            justify-content: flex-start;
        }

        .center {
            text-align: center;
        }

        .icon {
            font-size: clamp(44px, 5vw, 60px);
            margin-bottom: 8px;
            line-height: 1;
        }

        h2 {
            margin: 0 0 8px;
            color: #850000;
            font-size: clamp(20px, 2.2vw, 26px);
        }

        .subtitle {
            margin: 0 0 18px;
            color: #6b7280;
            font-size: clamp(13px, 1.3vw, 15px);
            line-height: 1.5;
        }

        /* Kotak Tanggal & Jam Digital Besar */
        .clock-box {
            background: #fdf2f2;
            border: 1px solid #f5c6cb;
            border-radius: 10px;
            padding: clamp(14px, 2vh, 20px) 16px;
            margin: 8px 0 22px;
        }

        #tanggal {
            margin: 0 0 6px;
            color: #850000;
            font-size: clamp(14px, 1.5vw, 18px);
            font-weight: 700;
        }

        #jam {
            margin: 0;
            color: #111827;
            font-size: clamp(30px, 4vw, 48px);
            font-weight: 800;
            font-variant-numeric: tabular-nums;
            letter-spacing: 1.5px;
            line-height: 1.1;
        }

        label {
            display: block;
            font-size: clamp(13px, 1.3vw, 15px);
            font-weight: 700;
            text-align: left;
            margin: 0 0 8px;
            color: #374151;
        }

        input {
            width: 100%;
            padding: clamp(12px, 1.6vh, 16px) 14px;
            border: 2px solid #d1d5db;
            border-radius: 8px;
            font-size: clamp(15px, 1.5vw, 18px);
            outline: none;
            color: #111827;
            background: #ffffff;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input:focus {
            border-color: #850000;
            box-shadow: 0 0 0 3px rgba(133, 0, 0, 0.14);
        }

        button {
            width: 100%;
            margin-top: 14px;
            padding: clamp(13px, 1.7vh, 16px);
            border: none;
            border-radius: 8px;
            background: #850000;
            color: #ffffff;
            font-size: clamp(14px, 1.4vw, 16px);
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
        }

        button:hover {
            background: #690000;
        }

        button:disabled {
            background: #b98a8a;
            cursor: wait;
        }

        .helper-text {
            margin: 14px 0 0;
            color: #6b7280;
            font-size: clamp(12px, 1.2vw, 13px);
        }

        /* Kotak Hasil Scan */
        .result {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-radius: 10px;
            padding: clamp(20px, 2.5vw, 28px);
            margin-top: 12px;
            background: #f9fafb;
            border: 2px dashed #d1d5db;
            overflow-wrap: anywhere;
            font-size: clamp(14px, 1.4vw, 16px);
            color: #6b7280;
            text-align: center;
        }

        .result h3 {
            margin: 0 0 12px;
            font-size: clamp(17px, 1.8vw, 21px);
        }

        .result p {
            margin: 6px 0;
            line-height: 1.6;
        }

        /* Kondisi Hadir Tepat Waktu (Hijau) */
        .result.success {
            background: #f0fdf4;
            border: 2px solid #86efac;
            color: #166534;
            text-align: left;
        }

        /* Kondisi Terlambat atau Error (Merah) */
        .result.late,
        .result.error {
            background: #fef2f2;
            border: 2px solid #fca5a5;
            color: #b91c1c;
            text-align: left;
        }

        .result.error {
            text-align: center;
        }

        .student-name {
            font-size: clamp(22px, 2.6vw, 30px);
            font-weight: 800;
            margin: 8px 0 12px;
            color: #15803d;
        }

        .student-name.late {
            color: #dc2626;
        }

        .status-text {
            font-weight: 700;
            font-size: clamp(15px, 1.6vw, 18px);
            margin-top: 10px;
        }

        .status-text.late {
            color: #dc2626;
        }

        /* Responsif untuk Tablet dan HP */
        @media (max-width: 820px) {
            main {
                grid-template-columns: 1fr;
                width: 92%;
                margin: 20px auto;
            }

            .result {
                min-height: 200px;
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
            <p class="subtitle">
                Tempelkan kartu RFID pada reader untuk mencatat kehadiran.
            </p>

            <div class="clock-box">
                <p id="tanggal"></p>
                <p id="jam"></p>
            </div>

            <form id="scanForm">
                <label for="uid">UID Kartu RFID</label>
                <input
                    type="text"
                    id="uid"
                    name="uid"
                    placeholder="Tempelkan kartu atau masukkan UID..."
                    autocomplete="off"
                    autofocus
                    required
                    maxlength="100"
                >

                <button type="submit" id="scanButton">
                    Proses Absensi
                </button>
            </form>

            <p class="helper-text">
                Pastikan kolom UID aktif saat menggunakan reader USB.
            </p>
        </section>

        <section class="card result-card">
            <h2>Hasil Scan Terakhir</h2>

            <div id="hasil" class="result" aria-live="polite">
                Menunggu kartu dipindai...
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

            const waktuString = sekarang.toLocaleTimeString('id-ID', {
                timeZone: 'Asia/Jakarta',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            }).replace(/\./g, ':');

            document.getElementById('jam').textContent = waktuString + ' WIB';
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
                judul.textContent = data.message || 'Hasil Scan';

                const detail = document.createElement('div');

                if (data.success && data.siswa) {
                    const statusText = (data.absensi && data.absensi.status)
                        ? String(data.absensi.status)
                        : '';
                    const isTerlambat = statusText.toLowerCase().includes('terlambat');

                    hasil.className = isTerlambat ? 'result late' : 'result success';

                    const nama = document.createElement('p');
                    nama.className = isTerlambat ? 'student-name late' : 'student-name';
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
                        status.className = isTerlambat ? 'status-text late' : 'status-text';
                        status.textContent = 'Status: ' + statusText;
                        detail.appendChild(status);

                        if (data.absensi.jam_masuk) {
                            const jamMasuk = document.createElement('p');
                            jamMasuk.textContent = 'Waktu Scan: ' + data.absensi.jam_masuk + ' WIB';
                            detail.appendChild(jamMasuk);
                        }
                    }
                } else {
                    hasil.className = 'result error';
                }

                hasil.append(judul, detail);

            } catch (error) {
                hasil.className = 'result error';
                hasil.textContent = 'Tidak dapat menghubungi server. Pastikan Laravel berjalan.';
            } finally {
                sedangProses = false;
                scanButton.disabled = false;
                scanButton.textContent = 'Proses Absensi';
                uidInput.value = '';
                uidInput.focus();
            }
        });

        // Kembalikan fokus otomatis ke kolom UID agar siap untuk scan berikutnya
        document.addEventListener('click', function (event) {
            if (!event.target.closest('input, button')) {
                uidInput.focus();
            }
        });
    </script>
</body>
</html>