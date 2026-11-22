@extends('layouts.app')

@section('title', 'Laporan Absensi - Absensi RFID')

@section('page-heading', 'Laporan Absensi')

@section('content')

@php
    $tanggalIndonesia = now()->locale('id')->translatedFormat('l, d F Y');
    $tanggalCetakIndonesia = now()->locale('id')->translatedFormat('d F Y');
@endphp

<style>
    .report-page {
        width: 100%;
        color: #1f2937;
        font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, sans-serif;
        font-size: 13px;
    }

    /* ================= HEADER HALAMAN ================= */
    .report-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 18px 22px;
        margin-bottom: 20px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
    }

    .report-heading h2 {
        margin: 0 0 4px;
        color: #850000;
        font-family: inherit;
        font-size: 19px;
        font-weight: 700;
        letter-spacing: -0.015em;
    }

    .report-heading p {
        margin: 0;
        color: #6b7280;
        font-size: 13px;
        font-weight: 500;
    }

    .report-period {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
        padding: 4px 10px;
        border-radius: 6px;
        background: #f3f4f6;
        color: #374151;
        font-size: 12px;
        font-weight: 600;
    }

    .report-heading-right {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .report-date {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 14px;
        border-radius: 8px;
        background: #fdf2f2;
        border: 1px solid #f5c6cb;
        color: #850000;
        font-size: 12.5px;
        font-weight: 700;
        white-space: nowrap;
    }

    .report-date svg {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }

    /* ================= TOMBOL ================= */
    .report-btn {
        min-height: 38px;
        padding: 0 15px;
        border: 1px solid #850000;
        border-radius: 8px;
        background: #850000;
        color: #ffffff;
        font-family: inherit;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 7px;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.18s ease;
    }

    .report-btn:hover {
        background: #680000;
        border-color: #680000;
        color: #ffffff;
    }

    .report-btn.secondary {
        border-color: #d1d5db;
        background: #ffffff;
        color: #374151;
    }

    .report-btn.secondary:hover {
        background: #f9fafb;
        border-color: #9ca3af;
        color: #1f2937;
    }

    /* Tombol Cetak / PDF yang menonjol & jelas */
    .report-btn.print-cta {
        background: #850000;
        border-color: #850000;
        color: #ffffff;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(133, 0, 0, 0.25);
    }

    .report-btn.print-cta:hover {
        background: #680000;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(133, 0, 0, 0.3);
    }

    .report-btn.print-header-btn {
        min-height: 36px;
        padding: 0 15px;
        background: #ffffff;
        border: 1px solid #ffffff;
        color: #850000;
        font-size: 12.5px;
        font-weight: 700;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.14);
    }

    .report-btn.print-header-btn:hover {
        background: #fef2f2;
        color: #680000;
    }

    .report-btn svg {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }

    /* ================= FILTER PANEL ================= */
    .report-filter-panel {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 18px 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
    }

    .report-filter-title {
        margin: 0 0 14px;
        color: #850000;
        font-size: 15px;
        font-weight: 700;
    }

    .report-filters {
        display: grid;
        grid-template-columns: 1fr 1fr 1.3fr auto;
        align-items: end;
        gap: 12px;
    }

    .report-filters label {
        display: flex;
        flex-direction: column;
        gap: 6px;
        color: #374151;
        font-size: 12px;
        font-weight: 600;
    }

    .report-filters input,
    .report-filters select {
        width: 100%;
        height: 38px;
        padding: 7px 11px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #ffffff;
        color: #1f2937;
        font-family: inherit;
        font-size: 13px;
        outline: none;
        transition: border-color 0.18s, box-shadow 0.18s;
    }

    .report-filters input:focus,
    .report-filters select:focus {
        border-color: #850000;
        box-shadow: 0 0 0 3px rgba(133, 0, 0, 0.1);
    }

    .report-filter-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* ================= KARTU RINGKASAN ================= */
    .report-section-title {
        margin: 0 0 12px;
        color: #850000;
        font-family: inherit;
        font-size: 15.5px;
        font-weight: 700;
    }

    .report-cards {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 22px;
    }

    .report-card {
        min-width: 0;
        padding: 14px 15px;
        border: 1px solid #e5e7eb;
        border-left-width: 4px;
        border-radius: 10px;
        background: #ffffff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
    }

    .report-card-label {
        display: block;
        color: #4b5563;
        font-size: 12px;
        font-weight: 600;
    }

    .report-card-number {
        display: block;
        margin-top: 6px;
        font-size: 22px;
        font-weight: 800;
        line-height: 1.15;
    }

    .report-card.total { border-left-color: #850000; }
    .report-card.total .report-card-number { color: #850000; }

    .report-card.hadir { border-left-color: #16a34a; }
    .report-card.hadir .report-card-number { color: #15803d; }

    .report-card.terlambat { border-left-color: #d97706; }
    .report-card.terlambat .report-card-number { color: #b45309; }

    .report-card.sakit { border-left-color: #0891b2; }
    .report-card.sakit .report-card-number { color: #0e7490; }

    .report-card.izin { border-left-color: #2563eb; }
    .report-card.izin .report-card-number { color: #1d4ed8; }

    .report-card.alpa { border-left-color: #dc2626; }
    .report-card.alpa .report-card-number { color: #b91c1c; }

    .report-card small {
        display: block;
        margin-top: 5px;
        color: #6b7280;
        font-size: 11px;
    }

    /* ================= PANEL TABEL ================= */
    .report-table-panel {
        overflow: hidden;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }

    .report-table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding: 15px 20px;
        background: #850000;
        color: #ffffff;
    }

    .report-table-header h3 {
        margin: 0 0 3px;
        color: #ffffff;
        font-family: inherit;
        font-size: 15.5px;
        font-weight: 700;
    }

    .report-table-header p {
        margin: 0;
        color: #f3d1d1;
        font-size: 12px;
    }

    .report-table-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .report-table-wrap {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
        font-family: inherit;
    }

    .report-table th {
        padding: 12px 14px;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        color: #374151;
        font-size: 12px;
        font-weight: 700;
        text-align: left;
        white-space: nowrap;
    }

    .report-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #f3f4f6;
        color: #374151;
        font-size: 13px;
        vertical-align: middle;
    }

    .report-table tbody tr:hover {
        background: #fdf8f8;
    }

    .report-table tbody tr:last-child td {
        border-bottom: none;
    }

    .report-table .text-center {
        text-align: center;
    }

    .report-student-name {
        color: #111827;
        font-weight: 600;
    }

    .report-empty {
        padding: 32px 16px !important;
        color: #6b7280 !important;
        text-align: center;
        font-size: 13px;
    }

    .report-note {
        margin: 12px 0 0;
        color: #6b7280;
        font-size: 12px;
    }

    .report-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
        padding: 12px 16px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #ffffff;
        color: #6b7280;
        font-size: 12px;
    }

    .report-footer strong {
        color: #850000;
        font-weight: 700;
    }

    .report-errors {
        margin-bottom: 16px;
        padding: 12px 15px;
        border: 1px solid #fecaca;
        border-radius: 8px;
        background: #fef2f2;
        color: #991b1b;
        font-size: 13px;
    }

    /* ================= KHUSUS CETAK ================= */
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

    /* ================= RESPONSIVE TABLET & MOBILE ================= */
    @media (max-width: 1050px) {
        .report-cards {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .report-filters {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .report-filter-actions {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 768px) {
        .report-heading {
            padding: 16px;
            flex-direction: column;
            align-items: flex-start;
        }

        .report-heading h2 {
            font-size: 17px;
        }

        .report-heading-right {
            width: 100%;
            justify-content: space-between;
        }

        .report-table-header {
            padding: 14px 16px;
            flex-direction: column;
            align-items: flex-start;
        }

        .report-table-actions {
            width: 100%;
        }

        .report-btn.print-header-btn {
            width: 100%;
        }

        /* Ubah baris tabel menjadi kartu rapi di layar HP */
        .report-table thead {
            display: none;
        }

        .report-table,
        .report-table tbody,
        .report-table tr,
        .report-table td {
            display: block;
            width: 100%;
        }

        .report-table tbody tr {
            padding: 14px 16px;
            border-bottom: 1px solid #e5e7eb;
        }

        .report-table tbody tr:last-child {
            border-bottom: none;
        }

        .report-table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 5px 0;
            border-bottom: none;
            text-align: right !important;
        }

        .report-table td::before {
            content: attr(data-label);
            color: #6b7280;
            font-size: 12px;
            font-weight: 600;
            text-align: left;
            flex-shrink: 0;
        }

        .report-table td.report-col-no {
            display: none;
        }

        .report-table td.report-col-total {
            margin-top: 6px;
            padding-top: 8px;
            border-top: 1px dashed #e5e7eb;
            font-weight: 700;
            color: #850000;
        }

        .report-table td.report-empty {
            display: block;
            text-align: center !important;
            padding: 24px 12px !important;
        }

        .report-table td.report-empty::before {
            display: none;
        }
    }

    @media (max-width: 550px) {
        .report-filters {
            grid-template-columns: 1fr;
        }

        .report-filter-actions {
            grid-column: auto;
        }

        .report-filter-actions .report-btn {
            flex: 1;
        }

        .report-cards {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .report-heading-right {
            flex-direction: column;
            align-items: stretch;
        }

        .report-date,
        .report-btn.print-cta {
            width: 100%;
            justify-content: center;
        }

        .report-footer {
            flex-direction: column;
            align-items: flex-start;
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

        .sidebar,
        .topbar,
        footer,
        .no-print,
        .report-heading,
        .report-filter-panel,
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
        .print-school-identity p,
        .print-report-title h2 {
            color: #111 !important;
        }

        .print-summary {
            grid-template-columns: repeat(6, 1fr) !important;
            border: 1px solid #777 !important;
        }

        .print-summary-item {
            border-right: 1px solid #aaa !important;
        }

        .report-table-panel {
            border: none !important;
            border-radius: 0 !important;
            overflow: visible !important;
            box-shadow: none !important;
            margin-top: 8px;
        }

        .report-table-header {
            display: none !important;
        }

        .report-table-wrap {
            overflow: visible !important;
        }

        /* Kembalikan format tabel normal saat dicetak meski dibuka dari HP */
        .report-table {
            display: table !important;
            width: 100% !important;
            border-collapse: collapse !important;
            white-space: normal !important;
            font-size: 9px !important;
        }

        .report-table thead {
            display: table-header-group !important;
        }

        .report-table tbody {
            display: table-row-group !important;
        }

        .report-table tr {
            display: table-row !important;
            padding: 0 !important;
            border: none !important;
        }

        .report-table th,
        .report-table td {
            display: table-cell !important;
            width: auto !important;
        }

        .report-table td::before {
            display: none !important;
        }

        .report-table td.report-col-no {
            display: table-cell !important;
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
                <td>{{ $tanggalCetakIndonesia }}</td>
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

        <div class="report-heading-right">
            <div class="report-date">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <span>{{ $tanggalIndonesia }}</span>
            </div>  
        </div>
    </div>

    {{-- ERROR --}}
    @if ($errors->any())
        <div class="report-errors">
            <strong>Periksa kembali data:</strong>

            <ul style="margin: 6px 0 0 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FILTER LAPORAN --}}
    <section class="report-filter-panel no-print">
        <h3 class="report-filter-title">Filter Laporan Kehadiran</h3>

        <form method="GET"
              action="{{ route('laporan.absensi') }}"
              class="report-filters">

            <label>
                Tanggal Mulai
                <input type="date"
                       name="tanggal_mulai"
                       value="{{ request('tanggal_mulai', $tanggalMulai) }}"
                       required>
            </label>

            <label>
                Tanggal Selesai
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

            <div class="report-filter-actions">
                <button type="submit" class="report-btn">
                    ☷ Tampilkan Laporan
                </button>

                <a class="report-btn secondary"
                   href="{{ route('laporan.absensi') }}">
                    Reset
                </a>
            </div>
        </form>
    </section>

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
            <span class="report-card-label">Total Catatan</span>
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
                        class="report-btn print-header-btn"
                        onclick="window.print()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    <span>Cetak / PDF</span>
                </button>
            </div>
        </div>

        <div class="report-table-wrap">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th class="text-center">Hadir</th>
                        <th class="text-center">Terlambat</th>
                        <th class="text-center">Sakit</th>
                        <th class="text-center">Izin</th>
                        <th class="text-center">Alpa</th>
                        <th class="text-center">Total Catatan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($siswa as $index => $item)
                        <tr>
                            <td class="report-col-no" data-label="No.">
                                {{ $index + 1 }}
                            </td>

                            <td data-label="NISN">
                                {{ $item->nisn }}
                            </td>

                            <td data-label="Nama Siswa">
                                <span class="report-student-name">
                                    {{ $item->nama_lengkap }}
                                </span>
                            </td>

                            <td data-label="Kelas">
                                {{ $item->kelas->nama_kelas ?? '-' }}
                            </td>

                            <td class="text-center" data-label="Hadir">
                                {{ $item->absensi->where('status', 'hadir')->count() }}
                            </td>

                            <td class="text-center" data-label="Terlambat">
                                {{ $item->absensi->where('status', 'terlambat')->count() }}
                            </td>

                            <td class="text-center" data-label="Sakit">
                                {{ $item->absensi->where('status', 'sakit')->count() }}
                            </td>

                            <td class="text-center" data-label="Izin">
                                {{ $item->absensi->where('status', 'izin')->count() }}
                            </td>

                            <td class="text-center" data-label="Alpa">
                                {{ $item->absensi->where('status', 'alpa')->count() }}
                            </td>

                            <td class="text-center report-col-total" data-label="Total Catatan">
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

    <div class="report-footer no-print">
        <span>
            Dicetak pada {{ now()->format('d-m-Y H:i') }} WIB.
        </span>

        <strong>
            Gunakan tombol Cetak / PDF di atas untuk menyimpan laporan.
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
            <p>Sungai Bangek, {{ $tanggalCetakIndonesia }}</p>
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