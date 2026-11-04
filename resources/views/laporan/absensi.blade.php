
@extends('layouts.app')

@section('title', 'Laporan Absensi')
@section('page-heading', 'Laporan Absensi')

@section('content')
<style>
    .report-page {
        color: #333;
        font-family: Arial, sans-serif;
        font-size: 12px;
    }

    /* ================= TAMPILAN SISTEM ================= */

    .report-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 16px;
    }

    .report-heading h2 {
        margin: 0 0 5px;
        color: #850000;
        font-family: Georgia, serif;
        font-size: 19px;
        font-weight: 700;
    }

    .report-heading p {
        margin: 0;
        color: #888;
        font-size: 11px;
    }

    .report-date {
        padding: 7px 12px;
        border-radius: 5px;
        background: #850000;
        color: white;
        font-size: 10px;
        white-space: nowrap;
    }

    .report-period {
        margin-top: 10px;
        color: #777;
        font-size: 11px;
    }

    .report-filters {
        display: grid;
        grid-template-columns: 1fr 1fr 1.4fr auto auto;
        align-items: end;
        gap: 10px;
        margin: 18px 0;
    }

    .report-filters label {
        display: flex;
        flex-direction: column;
        gap: 6px;
        color: #666;
        font-size: 10px;
    }

    .report-filters input,
    .report-filters select {
        width: 100%;
        min-height: 34px;
        padding: 7px 9px;
        border: 1px solid #dedede;
        border-radius: 5px;
        background: #fff;
        color: #444;
        font-size: 11px;
        outline: none;
    }

    .report-filters input:focus,
    .report-filters select:focus {
        border-color: #850000;
    }

    .report-btn {
        min-height: 34px;
        padding: 0 12px;
        border: 1px solid #850000;
        border-radius: 5px;
        background: #850000;
        color: #fff;
        font-size: 10px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        white-space: nowrap;
    }

    .report-btn:hover {
        background: #690000;
        color: #fff;
    }

    .report-btn.secondary {
        border-color: #ddd;
        background: #fff;
        color: #555;
    }

    .report-btn.secondary:hover {
        background: #f5f5f5;
    }

    .report-section-title {
        margin: 20px 0 10px;
        color: #850000;
        font-family: Georgia, serif;
        font-size: 14px;
        font-weight: 700;
    }

    .report-cards {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 9px;
        margin-bottom: 20px;
    }

    .report-card {
        min-width: 0;
        padding: 12px;
        border: 1px solid #e8e5e5;
        border-radius: 7px;
        background: #fafafa;
    }

    .report-card-label {
        display: block;
        color: #777;
        font-size: 10px;
    }

    .report-card-number {
        display: block;
        margin-top: 5px;
        font-size: 20px;
        font-weight: 700;
        line-height: 1.2;
    }

    .report-card.total .report-card-number {
        color: #850000;
    }

    .report-card.hadir .report-card-number {
        color: #168044;
    }

    .report-card.terlambat .report-card-number {
        color: #b27b00;
    }

    .report-card.sakit .report-card-number {
        color: #168da0;
    }

    .report-card.izin .report-card-number {
        color: #7770a5;
    }

    .report-card.alpa .report-card-number {
        color: #b32626;
    }

    .report-card small {
        display: block;
        margin-top: 5px;
        color: #999;
        font-size: 9px;
    }

    .report-table-panel {
        overflow: hidden;
        border: 1px solid #e8e5e5;
        border-radius: 8px;
        background: #fff;
    }

    .report-table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border-bottom: 1px solid #eee;
    }

    .report-table-header h3 {
        margin: 0 0 4px;
        color: #850000;
        font-family: Georgia, serif;
        font-size: 13px;
    }

    .report-table-header p {
        margin: 0;
        color: #999;
        font-size: 9px;
    }

    .report-table-actions {
        display: flex;
        gap: 6px;
    }

    .report-table-actions .report-btn {
        min-height: 28px;
        padding: 0 9px;
        font-size: 9px;
    }

    .report-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
        white-space: nowrap;
        font-size: 10px;
    }

    .report-table th {
        padding: 10px 9px;
        background: #f8eeee;
        color: #666;
        font-size: 9px;
        font-weight: 600;
        text-align: left;
    }

    .report-table td {
        padding: 10px 9px;
        border-bottom: 1px solid #eee;
        color: #444;
    }

    .report-table tbody tr:hover {
        background: #fcf8f8;
    }

    .report-table tbody tr:last-child td {
        border-bottom: none;
    }

    .report-table .text-center {
        text-align: center;
    }

    .report-table .total-row td {
        background: #f8eeee;
        color: #850000;
        font-weight: 700;
    }

    .report-empty {
        padding: 25px !important;
        color: #999 !important;
        text-align: center;
    }

    .report-note {
        margin: 9px 0 0;
        color: #999;
        font-size: 9px;
    }

    .report-footer {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin-top: 14px;
        color: #999;
        font-size: 9px;
    }

    .report-footer strong {
        color: #850000;
        font-weight: 600;
    }

    .report-errors {
        margin-bottom: 14px;
        padding: 10px 14px;
        border: 1px solid #f1caca;
        border-radius: 6px;
        background: #fff5f5;
        color: #b91c1c;
        font-size: 11px;
    }

    /* ================= KHUSUS CETAK ================= */

    /* Semua elemen cetak disembunyikan dari halaman sistem */
    .print-only {
        display: none !important;
    }

    .print-school-header {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 18px;
        padding-bottom: 12px;
        border-bottom: 3px double #222;
        text-align: center;
    }

    .print-school-icon {
        width: 65px;
        height: 65px;
        border: 2px solid #333;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        flex-shrink: 0;
    }

    .print-school-identity h2 {
        margin: 0 0 4px;
        color: #111;
        font-family: Georgia, serif;
        font-size: 19px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .print-school-identity p {
        margin: 2px 0;
        color: #333;
        font-size: 10px;
    }

    .print-report-title {
        margin: 18px 0 4px;
        text-align: center;
    }

    .print-report-title h2 {
        margin: 0;
        color: #111;
        font-family: Georgia, serif;
        font-size: 16px;
        text-transform: uppercase;
        text-decoration: underline;
        text-underline-offset: 4px;
    }

    .print-report-title p {
        margin: 8px 0 0;
        color: #333;
        font-size: 10px;
    }

    .print-information {
        margin: 16px 0 12px;
        font-size: 10px;
    }

    .print-information table {
        border-collapse: collapse;
    }

    .print-information td {
        padding: 3px 5px;
        vertical-align: top;
    }

    .print-summary-title {
        margin: 14px 0 7px;
        font-size: 11px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .print-summary {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        border: 1px solid #777;
        margin-bottom: 15px;
    }

    .print-summary-item {
        padding: 8px 4px;
        text-align: center;
        border-right: 1px solid #aaa;
    }

    .print-summary-item:last-child {
        border-right: none;
    }

    .print-summary-item span {
        display: block;
        font-size: 9px;
        margin-bottom: 4px;
    }

    .print-summary-item strong {
        display: block;
        font-size: 15px;
    }

    .print-signatures {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        margin-top: 35px;
        page-break-inside: avoid;
        break-inside: avoid;
        font-size: 10px;
        text-align: center;
    }

    .print-signature {
        min-height: 115px;
    }

    .print-signature p {
        margin: 3px 0;
    }

    .print-signature .signature-space {
        height: 65px;
    }

    .print-signature strong {
        text-decoration: underline;
    }

    /* ================= RESPONSIVE ================= */

    @media (max-width: 900px) {
        .report-filters {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .report-cards {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 550px) {
        .report-heading {
            flex-direction: column;
        }

        .report-filters {
            grid-template-columns: 1fr 1fr;
        }

        .report-cards {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .report-table-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .report-footer {
            flex-direction: column;
        }
    }

    /* ================= MODE CETAK PDF ================= */

    @media print {
        @page {
            size: A4 landscape;
            margin: 12mm 13mm 15mm;
        }

        html,
        body {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
            color: #111 !important;
            font-family: Arial, sans-serif !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Hilangkan elemen layout sistem */
        .sidebar,
        .topbar,
        footer,
        .no-print,
        .report-heading,
        .report-section-title,
        .report-cards,
        .report-note,
        .report-footer,
        .report-errors {
            display: none !important;
        }

        .app,
        .main-area,
        .content {
            display: block !important;
            width: 100% !important;
            min-height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            max-width: none !important;
            background: #fff !important;
            box-shadow: none !important;
        }

        .report-page {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            color: #111 !important;
            font-size: 10px !important;
        }

        /* Tampilkan kop sekolah */
        .print-only.print-school-header {
            display: flex !important;
        }

        .print-only.print-report-title,
        .print-only.print-information,
        .print-only.print-summary-title,
        .print-only.print-document-footer {
            display: block !important;
        }

        .print-only.print-summary {
            display: grid !important;
        }

        .print-only.print-signatures {
            display: grid !important;
        }

        .print-school-header {
            padding-bottom: 12px;
            border-bottom: 3px double #222;
        }

        .print-school-identity h2,
        .print-school-identity p {
            color: #111 !important;
        }

        .print-report-title h2 {
            color: #111 !important;
        }

        /* Ringkasan PDF */
        .print-summary {
            grid-template-columns: repeat(6, 1fr) !important;
            border: 1px solid #777 !important;
        }

        .print-summary-item {
            border-right: 1px solid #aaa !important;
        }

        /* Tabel PDF */
        .report-table-panel {
            border: none !important;
            border-radius: 0 !important;
            overflow: visible !important;
            margin-top: 8px;
        }

        .report-table-header {
            display: none !important;
        }

        .report-table-wrap {
            overflow: visible !important;
        }

        .report-table {
            width: 100% !important;
            border-collapse: collapse !important;
            white-space: normal !important;
            font-size: 9px !important;
        }

        .report-table thead {
            display: table-header-group;
        }

        .report-table th {
            padding: 8px 6px !important;
            background: #eaeaea !important;
            color: #111 !important;
            border: 1px solid #555 !important;
            text-align: center !important;
            font-size: 9px !important;
        }

        .report-table td {
            padding: 7px 6px !important;
            color: #111 !important;
            border: 1px solid #999 !important;
            font-size: 9px !important;
        }

        .report-table tbody tr {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .report-table tbody tr:hover {
            background: #fff !important;
        }

        .report-empty {
            text-align: center !important;
        }

        /* Tanda tangan */
        .print-signatures {
            grid-template-columns: 1fr 1fr !important;
            gap: 40px !important;
            margin-top: 35px !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .print-document-footer {
            margin-top: 18px;
            padding-top: 7px;
            border-top: 1px solid #aaa;
            color: #555 !important;
            font-size: 8px;
            text-align: center;
        }
    }
</style>

<div class="report-page">

    {{-- =================================================
         BAGIAN KHUSUS CETAK: KOP SEKOLAH
    ================================================== --}}

    <div class="print-only print-school-header">
        <div class="print-school-icon">🏛</div>

        <div class="print-school-identity">
            <h2>SDN 37 Sungai Bangek</h2>
            <p>SEKOLAH DASAR NEGERI 37 SUNGAI BANGKEK</p>
            <p>Laporan Administrasi Kehadiran Peserta Didik</p>
        </div>
    </div>

    {{-- JUDUL LAPORAN CETAK --}}
    <div class="print-only print-report-title">
        <h2>Laporan Rekapitulasi Absensi Siswa</h2>
        <p>
            Tahun Pelajaran {{ date('Y') }}/{{ date('Y') + 1 }}
        </p>
    </div>

    {{-- INFORMASI LAPORAN CETAK --}}
    <div class="print-only print-information">
        <table>
            <tr>
                <td width="100">Periode laporan</td>
                <td width="10">:</td>
                <td>
                    {{ \Carbon\Carbon::parse($tanggalMulai)->format('d-m-Y') }}
                    s.d.
                    {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d-m-Y') }}
                </td>
            </tr>

            <tr>
                <td>Kelas</td>
                <td>:</td>
                <td>
                    @if ($kelasId)
                        {{ optional($kelas->firstWhere('id', $kelasId))->nama_kelas ?? 'Kelas terpilih' }}
                    @else
                        Semua kelas yang diizinkan
                    @endif
                </td>
            </tr>

            <tr>
                <td>Tanggal cetak</td>
                <td>:</td>
                <td>{{ now()->translatedFormat('d F Y') }}</td>
            </tr>
        </table>
    </div>

    {{-- =================================================
         BAGIAN TAMPILAN SISTEM
    ================================================== --}}

    <div class="report-heading">
        <div>
            <h2>Laporan Kehadiran Siswa</h2>
            <p>Lihat rekap kehadiran dan unduh laporan sekolah.</p>

            <div class="report-period">
                Periode:
                {{ \Carbon\Carbon::parse($tanggalMulai)->format('d-m-Y') }}
                sampai
                {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d-m-Y') }}
            </div>
        </div>

        <div class="report-date">
            {{ now()->translatedFormat('l, d F Y') }}
        </div>
    </div>

    {{-- ERROR --}}
    @if ($errors->any())
        <div class="report-errors">
            <strong>Periksa kembali data:</strong>

            <ul style="margin: 6px 0 0 16px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FILTER LAPORAN --}}
    <form method="GET"
          action="{{ route('laporan.absensi') }}"
          class="report-filters no-print">

        <label>
            Tanggal mulai
            <input type="date"
                   name="tanggal_mulai"
                   value="{{ request('tanggal_mulai', $tanggalMulai) }}"
                   required>
        </label>

        <label>
            Tanggal selesai
            <input type="date"
                   name="tanggal_selesai"
                   value="{{ request('tanggal_selesai', $tanggalSelesai) }}"
                   required>
        </label>

        <label>
            Kelas
            <select name="kelas_id">
                <option value="">Semua kelas yang diizinkan</option>

                @foreach ($kelas as $itemKelas)
                    <option value="{{ $itemKelas->id }}"
                        @selected((string) $kelasId === (string) $itemKelas->id)>
                        {{ $itemKelas->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </label>

        <button type="submit" class="report-btn">
            Tampilkan laporan
        </button>

        <a class="report-btn secondary"
           href="{{ route('laporan.absensi') }}">
            Reset
        </a>
    </form>

    {{-- =================================================
         RINGKASAN KHUSUS CETAK PDF
    ================================================== --}}

    <div class="print-only print-summary-title">
        Ringkasan Kehadiran
    </div>

    <div class="print-only print-summary">
        <div class="print-summary-item">
            <span>Total Catatan</span>
            <strong>{{ $rekap['total_catatan'] }}</strong>
        </div>

        <div class="print-summary-item">
            <span>Hadir</span>
            <strong>{{ $rekap['hadir'] }}</strong>
        </div>

        <div class="print-summary-item">
            <span>Terlambat</span>
            <strong>{{ $rekap['terlambat'] }}</strong>
        </div>

        <div class="print-summary-item">
            <span>Sakit</span>
            <strong>{{ $rekap['sakit'] }}</strong>
        </div>

        <div class="print-summary-item">
            <span>Izin</span>
            <strong>{{ $rekap['izin'] }}</strong>
        </div>

        <div class="print-summary-item">
            <span>Alpa</span>
            <strong>{{ $rekap['alpa'] }}</strong>
        </div>
    </div>

    {{-- RINGKASAN TAMPILAN SISTEM --}}
    <h3 class="report-section-title">Ringkasan Kehadiran</h3>

    <div class="report-cards">
        <div class="report-card total">
            <span class="report-card-label">Total catatan</span>
            <strong class="report-card-number">
                {{ $rekap['total_catatan'] }}
            </strong>
            <small>Seluruh catatan</small>
        </div>

        <div class="report-card hadir">
            <span class="report-card-label">Hadir</span>
            <strong class="report-card-number">
                {{ $rekap['hadir'] }}
            </strong>
            <small>Status hadir</small>
        </div>

        <div class="report-card terlambat">
            <span class="report-card-label">Terlambat</span>
            <strong class="report-card-number">
                {{ $rekap['terlambat'] }}
            </strong>
            <small>Status terlambat</small>
        </div>

        <div class="report-card sakit">
            <span class="report-card-label">Sakit</span>
            <strong class="report-card-number">
                {{ $rekap['sakit'] }}
            </strong>
            <small>Status sakit</small>
        </div>

        <div class="report-card izin">
            <span class="report-card-label">Izin</span>
            <strong class="report-card-number">
                {{ $rekap['izin'] }}
            </strong>
            <small>Status izin</small>
        </div>

        <div class="report-card alpa">
            <span class="report-card-label">Alpa</span>
            <strong class="report-card-number">
                {{ $rekap['alpa'] }}
            </strong>
            <small>Status alpa</small>
        </div>
    </div>

    {{-- =================================================
         TABEL LAPORAN
    ================================================== --}}

    <div class="report-table-panel">

        <div class="report-table-header">
            <div>
                <h3>Rincian Kehadiran per Siswa</h3>

                <p>
                    Periode
                    {{ \Carbon\Carbon::parse($tanggalMulai)->format('d-m-Y') }}
                    –
                    {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d-m-Y') }}
                </p>
            </div>

            <div class="report-table-actions no-print">
                <button type="button"
                        class="report-btn secondary"
                        onclick="window.print()">
                    Cetak / PDF
                </button>
            </div>
        </div>

        <div class="report-table-wrap">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>NISN</th>
                        <th>Nama siswa</th>
                        <th>Kelas</th>
                        <th class="text-center">Hadir</th>
                        <th class="text-center">Terlambat</th>
                        <th class="text-center">Sakit</th>
                        <th class="text-center">Izin</th>
                        <th class="text-center">Alpa</th>
                        <th class="text-center">Total catatan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($siswa as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->nisn }}</td>
                            <td>{{ $item->nama_lengkap }}</td>
                            <td>{{ $item->kelas->nama_kelas ?? '-' }}</td>

                            <td class="text-center">
                                {{ $item->absensi->where('status', 'hadir')->count() }}
                            </td>

                            <td class="text-center">
                                {{ $item->absensi->where('status', 'terlambat')->count() }}
                            </td>

                            <td class="text-center">
                                {{ $item->absensi->where('status', 'sakit')->count() }}
                            </td>

                            <td class="text-center">
                                {{ $item->absensi->where('status', 'izin')->count() }}
                            </td>

                            <td class="text-center">
                                {{ $item->absensi->where('status', 'alpa')->count() }}
                            </td>

                            <td class="text-center">
                                {{ $item->absensi->count() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="report-empty">
                                Tidak ada siswa yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <p class="report-note">
        Catatan: Siswa dengan nol catatan tidak otomatis dinyatakan alpa.
    </p>

    <div class="report-footer">
        <span>
            Dicetak pada {{ now()->format('d-m-Y H:i') }} WIB.
        </span>

        <strong>
            Gunakan tombol Cetak / PDF untuk menyimpan laporan.
        </strong>
    </div>

    {{-- =================================================
         TANDA TANGAN KHUSUS CETAK PDF
    ================================================== --}}

    <div class="print-only print-signatures">

        <div class="print-signature">
            <p>Mengetahui,</p>
            <p>Kepala SDN 37 Sungai Bangek</p>

            <div class="signature-space"></div>

            <strong>( ........................................ )</strong>
            <p>NIP. ....................................</p>
        </div>

        <div class="print-signature">
            <p>Sungai Bangek, {{ now()->translatedFormat('d F Y') }}</p>
            <p>Petugas Absensi</p>

            <div class="signature-space"></div>

            <strong>( ........................................ )</strong>
            <p>NIP. ....................................</p>
        </div>

    </div>

    {{-- FOOTER KHUSUS CETAK --}}
    <div class="print-only print-document-footer">
        Dokumen Laporan Absensi Siswa - SDN 37 Sungai Bangek
        | Dicetak {{ now()->format('d-m-Y H:i') }} WIB
    </div>

</div>
@endsection
