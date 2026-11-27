@extends('layouts.app')

@section('title', 'Laporan Absensi - Absensi RFID')

@section('page-heading', 'Laporan Absensi')

@section('content')

@php
    $now = now()->locale('id');
    $isGuru = auth()->user()->role === 'guru';
    $tanggalIndonesia = $now->translatedFormat('l, d F Y');
    $tanggalCetakIndonesia = $now->translatedFormat('d F Y');
    $periodeMulaiText = !empty($tanggalMulai) ? \Carbon\Carbon::parse($tanggalMulai)->format('d-m-Y') : '-';
    $periodeSelesaiText = !empty($tanggalSelesai) ? \Carbon\Carbon::parse($tanggalSelesai)->format('d-m-Y') : '-';
    $tahunPelajaran = $now->month >= 7
        ? $now->year . '/' . ($now->year + 1)
        : ($now->year - 1) . '/' . $now->year;

    // Data Kepala Sekolah (sesuaikan dengan data resmi sekolah)
    $namaKepsek = 'NAMA KEPALA SEKOLAH, S.Pd.';
    $nipKepsek  = '19700101 199501 2 001';

    // Deteksi kelas terpilih & wali kelas untuk cetak rekap
    $kelasTerpilih = $kelasId ? $kelas->firstWhere('id', $kelasId) : ($isGuru ? $kelas->first() : null);
    $namaWaliKelasRekap = $kelasTerpilih?->waliKelas?->username ?? null;
    $nipWaliKelasRekap  = $kelasTerpilih?->waliKelas?->nip ?? null;
@endphp

<style>
    @page {
        size: A4 portrait;
        margin: 12mm 14mm 14mm;
    }

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
        width: 15px;
        height: 15px;
        flex-shrink: 0;
    }

    .btn-print-student {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        padding: 5px 11px;
        border: 1px solid #e6caca;
        border-radius: 6px;
        background: #ffffff;
        color: #850000;
        font-family: inherit;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.18s ease;
    }

    .btn-print-student:hover {
        background: #850000;
        border-color: #850000;
        color: #ffffff;
    }

    .btn-print-student svg {
        width: 14px;
        height: 14px;
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

    .report-filters.guru-filters {
        grid-template-columns: 1fr 1fr auto;
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
        gap: 10px;
        flex-wrap: wrap;
    }

    .report-quick-search {
        height: 36px;
        min-width: 230px;
        padding: 0 12px;
        border: 1px solid rgba(255, 255, 255, 0.35);
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.14);
        color: #ffffff;
        font-family: inherit;
        font-size: 12.5px;
        outline: none;
        transition: all 0.18s ease;
    }

    .report-quick-search::placeholder {
        color: #f3d1d1;
    }

    .report-quick-search:focus {
        background: #ffffff;
        color: #1f2937;
        border-color: #ffffff;
    }

    .report-quick-search:focus::placeholder {
        color: #9ca3af;
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
        padding: 11px 14px;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        color: #374151;
        font-size: 12px;
        font-weight: 700;
        text-align: left;
        white-space: nowrap;
    }

    .report-table td {
        padding: 10px 14px;
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

    .count-pill {
        display: inline-block;
        min-width: 26px;
        padding: 2px 7px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        text-align: center;
    }

    .count-pill.zero {
        background: #f3f4f6;
        color: #9ca3af;
        font-weight: 500;
    }

    .count-pill.hadir { background: #dcfce7; color: #15803d; }
    .count-pill.terlambat { background: #fef3c7; color: #b45309; }
    .count-pill.sakit { background: #cffafe; color: #0e7490; }
    .count-pill.izin { background: #dbeafe; color: #1d4ed8; }
    .count-pill.alpa { background: #fee2e2; color: #b91c1c; }
    .count-pill.total { background: #fdf2f2; color: #850000; }

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

    /* ================= MODAL PRATINJAU LAPORAN PER SISWA ================= */
    .student-print-modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(17, 24, 39, 0.65);
        backdrop-filter: blur(2px);
        padding: 20px;
        overflow-y: auto;
        align-items: flex-start;
        justify-content: center;
    }

    .student-print-modal.is-open {
        display: flex;
    }

    .student-print-dialog {
        width: 100%;
        max-width: 820px;
        background: #ffffff;
        border-radius: 14px;
        box-shadow: 0 20px 45px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        margin: auto;
    }

    .student-print-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        padding: 14px 20px;
        background: #850000;
        color: #ffffff;
    }

    .student-print-toolbar-title {
        font-size: 14.5px;
        font-weight: 700;
    }

    .student-print-toolbar-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .modal-btn {
        min-height: 34px;
        padding: 0 13px;
        border-radius: 7px;
        border: 1px solid transparent;
        font-family: inherit;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.16s ease;
    }

    .modal-btn svg {
        width: 14px;
        height: 14px;
        flex-shrink: 0;
    }

    .modal-btn-primary {
        background: #ffffff;
        color: #850000;
    }

    .modal-btn-primary:hover {
        background: #fdf2f2;
    }

    .modal-btn-wa {
        background: #16a34a;
        color: #ffffff;
        border-color: #15803d;
    }

    .modal-btn-wa:hover {
        background: #15803d;
    }

    .modal-btn-close {
        background: rgba(255, 255, 255, 0.14);
        color: #ffffff;
        border-color: rgba(255, 255, 255, 0.28);
    }

    .modal-btn-close:hover {
        background: rgba(255, 255, 255, 0.24);
    }

    /* Kertas Pratinjau Laporan Siswa */
    .single-student-sheet {
        padding: 28px 34px;
        background: #ffffff;
        color: #111827;
    }

    .single-student-info-box {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px 24px;
        margin: 16px 0;
        padding: 12px 16px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #f9fafb;
        font-size: 12.5px;
    }

    .single-student-info-row {
        display: flex;
        gap: 8px;
        min-width: 0;
    }

    .single-student-info-label {
        width: 115px;
        color: #4b5563;
        font-weight: 600;
        flex-shrink: 0;
    }

    .single-student-info-val {
        color: #111827;
        font-weight: 700;
        word-break: break-word;
    }

    .single-student-summary {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        border: 1px solid #9ca3af;
        border-radius: 6px;
        overflow: hidden;
        margin-bottom: 18px;
    }

    .single-student-summary-item {
        padding: 9px 6px;
        text-align: center;
        border-right: 1px solid #d1d5db;
        background: #ffffff;
    }

    .single-student-summary-item:last-child {
        border-right: none;
    }

    .single-student-summary-item span {
        display: block;
        font-size: 11px;
        color: #4b5563;
        margin-bottom: 3px;
    }

    .single-student-summary-item strong {
        display: block;
        font-size: 16px;
        color: #111827;
    }

    .single-detail-title {
        margin: 0 0 8px;
        font-size: 12.5px;
        font-weight: 700;
        color: #111827;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    .single-detail-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    .single-detail-table th {
        padding: 8px 10px;
        background: #f3f4f6;
        border: 1px solid #9ca3af;
        color: #111827;
        font-weight: 700;
        text-align: center;
    }

    .single-detail-table td {
        padding: 7px 10px;
        border: 1px solid #d1d5db;
        color: #1f2937;
    }

    .single-status-pill {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
    }

    .single-status-hadir { background: #dcfce7; color: #15803d; }
    .single-status-terlambat { background: #fef3c7; color: #b45309; }
    .single-status-sakit { background: #cffafe; color: #0e7490; }
    .single-status-izin { background: #dbeafe; color: #1d4ed8; }
    .single-status-alpa { background: #fee2e2; color: #b91c1c; }

    /* ================= KOP SURAT RESMI SEKOLAH ================= */
    .print-only {
        display: none !important;
    }

    .print-school-header {
        display: grid;
        grid-template-columns: 74px 1fr 74px;
        align-items: center;
        gap: 10px;
        padding-bottom: 9px;
        margin-bottom: 6px;
        border-bottom: 3px solid #111111;
        position: relative;
        text-align: center;
    }

    .print-school-header::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        bottom: -6px;
        border-bottom: 1px solid #111111;
    }

    .print-school-logo {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .print-school-logo img {
        width: 68px;
        height: 68px;
        object-fit: contain;
        display: block;
    }

    .print-school-identity {
        font-family: 'Times New Roman', Times, Georgia, serif;
        color: #111111;
        line-height: 1.25;
    }

    .print-school-identity .kop-pemkot {
        margin: 0;
        font-size: 13.5px;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-transform: uppercase;
    }

    .print-school-identity .kop-dinas {
        margin: 1px 0;
        font-size: 14.5px;
        font-weight: 700;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }

    .print-school-identity .kop-sekolah {
        margin: 2px 0;
        font-size: 15.5px;
        font-weight: 800;
        letter-spacing: 0.2px;
        text-transform: uppercase;
    }

    .print-school-identity .kop-kecamatan {
        margin: 1px 0 3px;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .print-school-identity .kop-alamat {
        margin: 0;
        font-size: 10px;
        font-style: italic;
        font-weight: 400;
        color: #111111;
    }

    .print-report-title {
        margin: 18px 0 4px;
        text-align: center;
    }

    .print-report-title h2 {
        margin: 0;
        color: #111;
        font-family: 'Times New Roman', Times, Georgia, serif;
        font-size: 14.5px;
        font-weight: 700;
        text-transform: uppercase;
        text-decoration: underline;
        text-underline-offset: 4px;
    }

    .print-report-title p {
        margin: 5px 0 0;
        color: #333;
        font-size: 10.5px;
    }

    .print-information {
        margin: 12px 0 10px;
        font-size: 10.5px;
    }

    .print-information table {
        border-collapse: collapse;
    }

    .print-information td {
        padding: 2px 5px;
        vertical-align: top;
    }

    .print-summary-title {
        margin: 10px 0 6px;
        font-size: 10.5px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .print-summary {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        border: 1px solid #777;
        margin-bottom: 12px;
    }

    .print-summary-item {
        padding: 6px 4px;
        text-align: center;
        border-right: 1px solid #aaa;
    }

    .print-summary-item:last-child {
        border-right: none;
    }

    .print-summary-item span {
        display: block;
        font-size: 8.5px;
        margin-bottom: 3px;
    }

    .print-summary-item strong {
        display: block;
        font-size: 13.5px;
    }

    .print-signatures {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-top: 26px;
        page-break-inside: avoid;
        break-inside: avoid;
        font-size: 10.5px;
        text-align: center;
    }

    .print-signature {
        min-height: 105px;
    }

    .print-signature p {
        margin: 2px 0;
    }

    .print-signature .signature-space {
        height: 56px;
    }

    .print-signature strong {
        text-decoration: underline;
    }

    /* ================= RESPONSIVE TABLET & MOBILE ================= */
    @media (max-width: 1050px) {
        .report-cards {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .report-filters,
        .report-filters.guru-filters {
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

        .report-quick-search,
        .report-btn.print-header-btn {
            width: 100%;
        }

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

        .report-table td.report-col-actions {
            margin-top: 8px;
            padding-top: 10px;
            border-top: 1px dashed #e5e7eb;
            justify-content: flex-end;
        }

        .report-table td.report-col-actions::before {
            display: none;
        }

        .btn-print-student {
            width: 100%;
            padding: 8px 12px;
        }

        .report-table td.report-empty {
            display: block;
            text-align: center !important;
            padding: 24px 12px !important;
        }

        .report-table td.report-empty::before {
            display: none;
        }

        .student-print-modal {
            padding: 10px;
        }

        .single-student-sheet {
            padding: 18px 16px;
        }

        .print-school-header {
            grid-template-columns: 52px 1fr 52px;
            gap: 8px;
        }

        .print-school-logo img {
            width: 48px;
            height: 48px;
        }

        .print-school-identity .kop-pemkot { font-size: 11px; }
        .print-school-identity .kop-dinas { font-size: 11.5px; }
        .print-school-identity .kop-sekolah { font-size: 12.5px; }
        .print-school-identity .kop-kecamatan { font-size: 10.5px; }
        .print-school-identity .kop-alamat { font-size: 9px; }

        .single-student-info-box {
            grid-template-columns: 1fr;
        }

        .single-student-summary {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 550px) {
        .report-filters,
        .report-filters.guru-filters {
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

        .report-date {
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
        html,
        body {
            width: 100% !important;
            height: auto !important;
            overflow: visible !important;
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
            height: auto !important;
            min-height: 0 !important;
            overflow: visible !important;
            margin: 0 !important;
            padding: 0 !important;
            max-width: none !important;
            background: #fff !important;
            box-shadow: none !important;
        }

        .report-page {
            width: 100% !important;
            height: auto !important;
            overflow: visible !important;
            margin: 0 !important;
            padding: 0 !important;
            color: #111 !important;
            font-size: 9.5px !important;
        }

        .count-pill {
            background: transparent !important;
            color: #111 !important;
            padding: 0 !important;
            min-width: auto !important;
            font-size: inherit !important;
        }

        .print-school-header {
            grid-template-columns: 68px 1fr 68px !important;
            gap: 8px !important;
        }

        .print-school-logo img {
            width: 64px !important;
            height: 64px !important;
        }

        .print-school-identity .kop-pemkot { font-size: 13px !important; }
        .print-school-identity .kop-dinas { font-size: 14px !important; }
        .print-school-identity .kop-sekolah { font-size: 15px !important; }
        .print-school-identity .kop-kecamatan { font-size: 12.5px !important; }
        .print-school-identity .kop-alamat { font-size: 9.5px !important; }

        body:not(.print-single-student) .student-print-modal {
            display: none !important;
        }

        body:not(.print-single-student) .print-only.print-school-header {
            display: grid !important;
        }

        body:not(.print-single-student) .print-only.print-report-title,
        body:not(.print-single-student) .print-only.print-information,
        body:not(.print-single-student) .print-only.print-summary-title,
        body:not(.print-single-student) .print-only.print-document-footer {
            display: block !important;
        }

        body:not(.print-single-student) .print-only.print-summary,
        body:not(.print-single-student) .print-only.print-signatures {
            display: grid !important;
        }

        body:not(.print-single-student) .report-table-panel {
            border: none !important;
            border-radius: 0 !important;
            overflow: visible !important;
            box-shadow: none !important;
            margin-top: 6px;
        }

        body:not(.print-single-student) .report-table-header {
            display: none !important;
        }

        body:not(.print-single-student) .report-table-wrap {
            overflow: visible !important;
        }

        body:not(.print-single-student) .report-table {
            display: table !important;
            width: 100% !important;
            border-collapse: collapse !important;
            table-layout: auto !important;
            white-space: normal !important;
            font-size: 8.5px !important;
        }

        body:not(.print-single-student) .report-table thead {
            display: table-header-group !important;
        }

        body:not(.print-single-student) .report-table tbody {
            display: table-row-group !important;
        }

        body:not(.print-single-student) .report-table tr {
            display: table-row !important;
            padding: 0 !important;
            border: none !important;
        }

        body:not(.print-single-student) .report-table th,
        body:not(.print-single-student) .report-table td {
            display: table-cell !important;
            width: auto !important;
        }

        body:not(.print-single-student) .report-table th.no-print,
        body:not(.print-single-student) .report-table td.no-print {
            display: none !important;
        }

        body:not(.print-single-student) .report-table td::before {
            display: none !important;
        }

        body:not(.print-single-student) .report-table td.report-col-no {
            display: table-cell !important;
        }

        body:not(.print-single-student) .report-table th {
            padding: 6px 4px !important;
            background: #eaeaea !important;
            color: #111 !important;
            border: 1px solid #555 !important;
            text-align: center !important;
            font-size: 8.5px !important;
            white-space: nowrap !important;
        }

        body:not(.print-single-student) .report-table td {
            padding: 5px 4px !important;
            color: #111 !important;
            border: 1px solid #999 !important;
            font-size: 8.5px !important;
        }

        body:not(.print-single-student) .report-table tbody tr {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        /* --- MODE B: CETAK LAPORAN INDIVIDU PER SISWA --- */
        body.print-single-student .report-table-panel,
        body.print-single-student .print-only {
            display: none !important;
        }

        body.print-single-student .student-print-modal {
            display: block !important;
            position: static !important;
            inset: auto !important;
            width: 100% !important;
            height: auto !important;
            background: #ffffff !important;
            padding: 0 !important;
            margin: 0 !important;
            overflow: visible !important;
        }

        body.print-single-student .student-print-dialog {
            max-width: 100% !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }

        body.print-single-student .student-print-toolbar {
            display: none !important;
        }

        body.print-single-student .single-student-sheet {
            padding: 0 !important;
        }

        body.print-single-student .single-student-info-box {
            grid-template-columns: 1fr 1fr !important;
            background: #f9fafb !important;
            border: 1px solid #777 !important;
        }

        body.print-single-student .single-student-summary {
            grid-template-columns: repeat(6, 1fr) !important;
            border: 1px solid #777 !important;
        }

        body.print-single-student .single-detail-table th {
            background: #eaeaea !important;
            border: 1px solid #555 !important;
            color: #111 !important;
        }

        body.print-single-student .single-detail-table td {
            border: 1px solid #888 !important;
            color: #111 !important;
        }

        .print-document-footer {
            margin-top: 14px;
            padding-top: 6px;
            border-top: 1px solid #aaa;
            color: #555 !important;
            font-size: 8px;
            text-align: center;
        }
    }
</style>

<div class="report-page">

    {{-- =================================================
         BAGIAN KHUSUS CETAK REKAP KELAS
    ================================================== --}}
    <div class="print-only print-school-header">
        <div class="print-school-logo">
            <img src="{{ asset('images/logo-tut-wuri.png') }}"
                 alt="Logo Tut Wuri Handayani"
                 onerror="this.style.visibility='hidden'">
        </div>

        <div class="print-school-identity">
            <p class="kop-pemkot">PEMERINTAH KOTA PADANG</p>
            <p class="kop-dinas">DINAS PENDIDIKAN DAN KEBUDAYAAN</p>
            <h2 class="kop-sekolah">UPTD SEKOLAH DASAR NEGERI 37 SUNGAI BANGEK</h2>
            <p class="kop-kecamatan">KECAMATAN KOTO TANGAH</p>
            <p class="kop-alamat">Jalan Napak Tilas, Kotak Pos 25171, (E-Mail): sdnegeri37sungaibangek@gmail.com</p>
        </div>

        <div class="print-school-logo">
            <img src="{{ asset('images/logo-padang.png') }}"
                 alt="Logo Kota Padang"
                 onerror="this.style.visibility='hidden'">
        </div>
    </div>

    {{-- JUDUL LAPORAN CETAK --}}
    <div class="print-only print-report-title">
        <h2>Laporan Rekapitulasi Absensi Siswa</h2>
        <p>Tahun Pelajaran {{ $tahunPelajaran }}</p>
    </div>

    {{-- INFORMASI LAPORAN CETAK --}}
    <div class="print-only print-information">
        <table>
            <tr>
                <td width="100">Periode laporan</td>
                <td width="10">:</td>
                <td>{{ $periodeMulaiText }} s.d. {{ $periodeSelesaiText }}</td>
            </tr>

            <tr>
                <td>Kelas</td>
                <td>:</td>
                <td>
                    @if ($kelasTerpilih)
                        {{ $kelasTerpilih->nama_kelas }}
                    @elseif ($isGuru)
                        Kelas Diampu
                    @else
                        Semua Kelas
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
            <p>
                {{ $isGuru
                    ? 'Lihat rekap kehadiran dan cetak laporan kelas yang Anda ampu atau per siswa.'
                    : 'Lihat rekap kehadiran dan unduh laporan sekolah atau laporan per siswa.' }}
            </p>

            <div class="report-period">
                Periode: {{ $periodeMulaiText }} sampai {{ $periodeSelesaiText }}
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
              class="report-filters {{ $isGuru ? 'guru-filters' : '' }}">

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

            @unless ($isGuru)
                <label>
                    Kelas
                    <select name="kelas_id">
                        <option value="">Semua kelas</option>

                        @foreach ($kelas as $itemKelas)
                            <option value="{{ $itemKelas->id }}"
                                @selected((string) $kelasId === (string) $itemKelas->id)>
                                {{ $itemKelas->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </label>
            @endunless

            <div class="report-filter-actions">
                <button type="submit" class="report-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    <span>Tampilkan Laporan</span>
                </button>

                <a class="report-btn secondary"
                   href="{{ route('laporan.absensi') }}">
                    Reset
                </a>
            </div>
        </form>
    </section>

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

    <h3 class="report-section-title">Ringkasan Kehadiran</h3>

    <div class="report-cards">
        <div class="report-card total">
            <span class="report-card-label">Total Catatan</span>
            <strong class="report-card-number">{{ $rekap['total_catatan'] }}</strong>
            <small>Seluruh catatan</small>
        </div>

        <div class="report-card hadir">
            <span class="report-card-label">Hadir</span>
            <strong class="report-card-number">{{ $rekap['hadir'] }}</strong>
            <small>Status hadir</small>
        </div>

        <div class="report-card terlambat">
            <span class="report-card-label">Terlambat</span>
            <strong class="report-card-number">{{ $rekap['terlambat'] }}</strong>
            <small>Status terlambat</small>
        </div>

        <div class="report-card sakit">
            <span class="report-card-label">Sakit</span>
            <strong class="report-card-number">{{ $rekap['sakit'] }}</strong>
            <small>Status sakit</small>
        </div>

        <div class="report-card izin">
            <span class="report-card-label">Izin</span>
            <strong class="report-card-number">{{ $rekap['izin'] }}</strong>
            <small>Status izin</small>
        </div>

        <div class="report-card alpa">
            <span class="report-card-label">Alpa</span>
            <strong class="report-card-number">{{ $rekap['alpa'] }}</strong>
            <small>Status alpa</small>
        </div>
    </div>

    <div class="report-table-panel">

        <div class="report-table-header">
            <div>
                <h3>
                    Rincian Kehadiran per Siswa
                    @if ($isGuru && $kelasTerpilih)
                        — {{ $kelasTerpilih->nama_kelas }}
                    @endif
                </h3>
                <p>Periode {{ $periodeMulaiText }} – {{ $periodeSelesaiText }}</p>
            </div>

            <div class="report-table-actions no-print">
                <input type="text"
                       id="quickSearchSiswa"
                       class="report-quick-search"
                       placeholder="Cari nama atau NISN siswa...">

                <button type="button"
                        class="report-btn print-header-btn"
                        onclick="printRekapKelas()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    <span>Cetak Rekap / PDF</span>
                </button>
            </div>
        </div>

        <div class="report-table-wrap">
            <table class="report-table">
                <thead>
                    <tr>
                        <th class="text-center">No.</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th class="text-center">Kelas</th>
                        <th class="text-center">Hadir</th>
                        <th class="text-center">Terlambat</th>
                        <th class="text-center">Sakit</th>
                        <th class="text-center">Izin</th>
                        <th class="text-center">Alpa</th>
                        <th class="text-center">Total</th>
                        <th class="text-center no-print">Laporan Siswa</th>
                    </tr>
                </thead>

                <tbody id="reportTableBody">
                    @forelse ($siswa as $index => $item)
                        @php
                            $absensiSorted = $item->absensi->sortBy('tanggal')->values();
                            $detailAbsensi = $absensiSorted->map(function ($abs) {
                                $carbonTgl = $abs->tanggal instanceof \Carbon\CarbonInterface
                                    ? $abs->tanggal
                                    : \Carbon\Carbon::parse($abs->tanggal);

                                return [
                                    'hari' => $carbonTgl->locale('id')->translatedFormat('l'),
                                    'tanggal' => $carbonTgl->locale('id')->translatedFormat('d M Y'),
                                    'waktu_masuk' => $abs->waktu_masuk ? substr((string) $abs->waktu_masuk, 0, 8) : '-',
                                    'waktu_keluar' => $abs->waktu_keluar ? substr((string) $abs->waktu_keluar, 0, 8) : '-',
                                    'status' => ucfirst($abs->status),
                                    'status_raw' => $abs->status,
                                    'keterangan' => $abs->keterangan ?: '-',
                                ];
                            });

                            $studentPayload = [
                                'nama' => $item->nama_lengkap,
                                'nisn' => $item->nisn,
                                'kelas' => $item->kelas->nama_kelas ?? '-',
                                'wali_kelas' => $item->kelas?->waliKelas?->username ?? (auth()->user()->username ?? '( ........................................ )'),
                                'nip_wali_kelas' => $item->kelas?->waliKelas?->nip ?? (auth()->user()->nip ?? '....................................'),
                                'hadir' => $item->absensi->where('status', 'hadir')->count(),
                                'terlambat' => $item->absensi->where('status', 'terlambat')->count(),
                                'sakit' => $item->absensi->where('status', 'sakit')->count(),
                                'izin' => $item->absensi->where('status', 'izin')->count(),
                                'alpa' => $item->absensi->where('status', 'alpa')->count(),
                                'total' => $item->absensi->count(),
                                'riwayat' => $detailAbsensi,
                            ];
                        @endphp

                        <tr data-report-row
                            data-nama="{{ strtolower($item->nama_lengkap) }}"
                            data-nisn="{{ strtolower($item->nisn) }}">
                            <td class="report-col-no col-no text-center" data-label="No.">
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

                            <td class="text-center" data-label="Kelas">
                                {{ $item->kelas->nama_kelas ?? '-' }}
                            </td>

                            <td class="text-center" data-label="Hadir">
                                <span class="count-pill {{ $studentPayload['hadir'] > 0 ? 'hadir' : 'zero' }}">
                                    {{ $studentPayload['hadir'] }}
                                </span>
                            </td>

                            <td class="text-center" data-label="Terlambat">
                                <span class="count-pill {{ $studentPayload['terlambat'] > 0 ? 'terlambat' : 'zero' }}">
                                    {{ $studentPayload['terlambat'] }}
                                </span>
                            </td>

                            <td class="text-center" data-label="Sakit">
                                <span class="count-pill {{ $studentPayload['sakit'] > 0 ? 'sakit' : 'zero' }}">
                                    {{ $studentPayload['sakit'] }}
                                </span>
                            </td>

                            <td class="text-center" data-label="Izin">
                                <span class="count-pill {{ $studentPayload['izin'] > 0 ? 'izin' : 'zero' }}">
                                    {{ $studentPayload['izin'] }}
                                </span>
                            </td>

                            <td class="text-center" data-label="Alpa">
                                <span class="count-pill {{ $studentPayload['alpa'] > 0 ? 'alpa' : 'zero' }}">
                                    {{ $studentPayload['alpa'] }}
                                </span>
                            </td>

                            <td class="text-center report-col-total" data-label="Total Catatan">
                                <span class="count-pill {{ $studentPayload['total'] > 0 ? 'total' : 'zero' }}">
                                    {{ $studentPayload['total'] }}
                                </span>
                            </td>

                            <td class="text-center report-col-actions no-print" data-label="Laporan Siswa">
                                <button type="button"
                                        class="btn-print-student"
                                        data-student='@json($studentPayload)'
                                        onclick="openStudentPrintModal(this)">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                        <rect x="6" y="14" width="12" height="8"></rect>
                                    </svg>
                                    <span>Cetak</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="report-empty">
                                Tidak ada siswa yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse

                    <tr id="emptySearchReportRow" style="display: none;">
                        <td colspan="11" class="report-empty">
                            Tidak ada siswa yang sesuai dengan kata kunci pencarian.
                        </td>
                    </tr>
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
            Klik "Cetak" pada baris siswa untuk mengunduh PDF laporan individu wali murid.
        </strong>
    </div>

    <div class="print-only print-signatures">
        <div class="print-signature">
            <p>Mengetahui,</p>
            <p>Kepala UPTD SDN 37 Sungai Bangek</p>

            <div class="signature-space"></div>

            <strong>{{ $namaKepsek }}</strong>
            <p>NIP. {{ $nipKepsek }}</p>
        </div>

        <div class="print-signature">
            <p>Sungai Bangek, {{ $tanggalCetakIndonesia }}</p>
            <p>
                @if ($kelasTerpilih && ($namaWaliKelasRekap || $isGuru))
                    Wali Kelas {{ $kelasTerpilih->nama_kelas }}
                @else
                    Petugas Absensi
                @endif
            </p>

            <div class="signature-space"></div>

            @if ($namaWaliKelasRekap)
                <strong>{{ $namaWaliKelasRekap }}</strong>
                <p>NIP. {{ $nipWaliKelasRekap ?: '....................................' }}</p>
            @else
                <strong>{{ auth()->user()->username ?? '( ........................................ )' }}</strong>
                <p>NIP. {{ auth()->user()->nip ?? '....................................' }}</p>
            @endif
        </div>
    </div>

    {{-- FOOTER KHUSUS CETAK --}}
    <div class="print-only print-document-footer">
        Dokumen Laporan Absensi Siswa - UPTD SDN 37 Sungai Bangek
        | Dicetak {{ now()->format('d-m-Y H:i') }} WIB
    </div>

</div>

{{-- MODAL PRATINJAU LAPORAN INDIVIDU SISWA --}}
<div id="studentPrintModal" class="student-print-modal" onclick="if(event.target === this) closeStudentPrintModal()">
    <div class="student-print-dialog">
        <div class="student-print-toolbar no-print">
            <div class="student-print-toolbar-title">
                Pratinjau Laporan Kehadiran Siswa (Wali Murid)
            </div>

            <div class="student-print-toolbar-actions">
                <button type="button" id="btnCopyWaText" class="modal-btn modal-btn-wa" onclick="copyStudentWhatsApp()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>
                    <span id="btnCopyWaLabel">Salin Pesan WA</span>
                </button>

                <button type="button" class="modal-btn modal-btn-primary" onclick="executeSingleStudentPrint()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    <span>Cetak / Simpan PDF</span>
                </button>

                <button type="button" class="modal-btn modal-btn-close" onclick="closeStudentPrintModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    <span>Tutup</span>
                </button>
            </div>
        </div>

        <div id="singleStudentPrintArea" class="single-student-sheet">
            {{-- KOP SURAT RESMI SEKOLAH --}}
            <div class="print-school-header">
                <div class="print-school-logo">
                    <img src="{{ asset('images/logo-tut-wuri.png') }}"
                         alt="Logo Tut Wuri Handayani"
                         onerror="this.style.visibility='hidden'">
                </div>

                <div class="print-school-identity">
                    <p class="kop-pemkot">PEMERINTAH KOTA PADANG</p>
                    <p class="kop-dinas">DINAS PENDIDIKAN DAN KEBUDAYAAN</p>
                    <h2 class="kop-sekolah">UPTD SEKOLAH DASAR NEGERI 37 SUNGAI BANGEK</h2>
                    <p class="kop-kecamatan">KECAMATAN KOTO TANGAH</p>
                    <p class="kop-alamat">Jalan Napak Tilas, Kotak Pos 25171, (E-Mail): sdnegeri37sungaibangek@gmail.com</p>
                </div>

                <div class="print-school-logo">
                    <img src="{{ asset('images/logo-padang.png') }}"
                         alt="Logo Kota Padang"
                         onerror="this.style.visibility='hidden'">
                </div>
            </div>

            <div class="print-report-title">
                <h2>Laporan Kehadiran Peserta Didik</h2>
                <p>Tahun Pelajaran {{ $tahunPelajaran }}</p>
            </div>

            <div class="single-student-info-box">
                <div class="single-student-info-row">
                    <span class="single-student-info-label">Nama Siswa</span>
                    <span>:</span>
                    <span class="single-student-info-val" id="spNama">-</span>
                </div>
                <div class="single-student-info-row">
                    <span class="single-student-info-label">Periode Laporan</span>
                    <span>:</span>
                    <span class="single-student-info-val">{{ $periodeMulaiText }} s.d. {{ $periodeSelesaiText }}</span>
                </div>
                <div class="single-student-info-row">
                    <span class="single-student-info-label">NISN</span>
                    <span>:</span>
                    <span class="single-student-info-val" id="spNisn">-</span>
                </div>
                <div class="single-student-info-row">
                    <span class="single-student-info-label">Tanggal Cetak</span>
                    <span>:</span>
                    <span class="single-student-info-val">{{ $tanggalCetakIndonesia }}</span>
                </div>
                <div class="single-student-info-row">
                    <span class="single-student-info-label">Kelas</span>
                    <span>:</span>
                    <span class="single-student-info-val" id="spKelas">-</span>
                </div>
            </div>

            <div class="single-detail-title">Ringkasan Kehadiran</div>
            <div class="single-student-summary">
                <div class="single-student-summary-item">
                    <span>Total Catatan</span>
                    <strong id="spTotal">0</strong>
                </div>
                <div class="single-student-summary-item">
                    <span>Hadir</span>
                    <strong id="spHadir">0</strong>
                </div>
                <div class="single-student-summary-item">
                    <span>Terlambat</span>
                    <strong id="spTerlambat">0</strong>
                </div>
                <div class="single-student-summary-item">
                    <span>Sakit</span>
                    <strong id="spSakit">0</strong>
                </div>
                <div class="single-student-summary-item">
                    <span>Izin</span>
                    <strong id="spIzin">0</strong>
                </div>
                <div class="single-student-summary-item">
                    <span>Alpa</span>
                    <strong id="spAlpa">0</strong>
                </div>
            </div>

            <div class="single-detail-title">Rincian Riwayat Kehadiran Harian</div>
            <table class="single-detail-table">
                <thead>
                    <tr>
                        <th width="42">No.</th>
                        <th>Hari / Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody id="spDetailBody">
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 16px; color: #6b7280;">
                            Memuat data...
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="print-signatures">
                <div class="print-signature">
                    <p>Mengetahui,</p>
                    <p>Orang Tua / Wali Murid</p>
                    <div class="signature-space"></div>
                    <strong>( ........................................ )</strong>
                </div>

                <div class="print-signature">
                    <p>Sungai Bangek, {{ $tanggalCetakIndonesia }}</p>
                    <p>Wali Kelas <span id="spLabelKelas"></span></p>
                    <div class="signature-space"></div>
                    <strong id="spWaliKelas">( ........................................ )</strong>
                    <p>NIP. <span id="spNipWaliKelas">....................................</span></p>
                </div>
            </div>

            <div class="print-document-footer">
                Dokumen Laporan Kehadiran Siswa - UPTD SDN 37 Sungai Bangek | Dicetak {{ now()->format('d-m-Y H:i') }} WIB
            </div>
        </div>
    </div>
</div>

<script>
    let activeStudentReport = null;

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text ?? '';
        return div.innerHTML;
    }

    function printRekapKelas() {
        document.body.classList.remove('print-single-student');
        window.print();
    }

    function openStudentPrintModal(button) {
        const rawData = button.getAttribute('data-student');
        if (!rawData) return;

        const data = JSON.parse(rawData);
        activeStudentReport = data;

        document.getElementById('spNama').textContent = data.nama;
        document.getElementById('spNisn').textContent = data.nisn;
        document.getElementById('spKelas').textContent = data.kelas;
        document.getElementById('spLabelKelas').textContent = data.kelas !== '-' ? data.kelas : '';
        document.getElementById('spWaliKelas').textContent = data.wali_kelas;
        document.getElementById('spNipWaliKelas').textContent = data.nip_wali_kelas;

        document.getElementById('spTotal').textContent = data.total;
        document.getElementById('spHadir').textContent = data.hadir;
        document.getElementById('spTerlambat').textContent = data.terlambat;
        document.getElementById('spSakit').textContent = data.sakit;
        document.getElementById('spIzin').textContent = data.izin;
        document.getElementById('spAlpa').textContent = data.alpa;

        const detailBody = document.getElementById('spDetailBody');
        detailBody.innerHTML = '';

        if (!data.riwayat || data.riwayat.length === 0) {
            detailBody.innerHTML = `
                <tr>
                    <td colspan="6" style="text-align: center; padding: 16px; color: #6b7280;">
                        Belum ada catatan kehadiran pada periode {{ $periodeMulaiText }} s.d. {{ $periodeSelesaiText }}.
                    </td>
                </tr>
            `;
        } else {
            data.riwayat.forEach(function (item, idx) {
                const tr = document.createElement('tr');
                const safeStatusRaw = String(item.status_raw || 'hadir').replace(/[^a-z0-9_-]/gi, '');
                const badgeClass = 'single-status-' + safeStatusRaw;

                tr.innerHTML = `
                    <td style="text-align: center;">${idx + 1}</td>
                    <td>${escapeHtml(item.hari)}, ${escapeHtml(item.tanggal)}</td>
                    <td style="text-align: center;">${escapeHtml(item.waktu_masuk)}</td>
                    <td style="text-align: center;">${escapeHtml(item.waktu_keluar)}</td>
                    <td style="text-align: center;">
                        <span class="single-status-pill ${badgeClass}">${escapeHtml(item.status)}</span>
                    </td>
                    <td>${escapeHtml(item.keterangan)}</td>
                `;
                detailBody.appendChild(tr);
            });
        }

        const btnWaLabel = document.getElementById('btnCopyWaLabel');
        if (btnWaLabel) {
            btnWaLabel.textContent = 'Salin Pesan WA';
        }

        const modal = document.getElementById('studentPrintModal');
        modal.classList.add('is-open');
        modal.scrollTop = 0;
        document.body.style.overflow = 'hidden';
    }

    function closeStudentPrintModal() {
        document.getElementById('studentPrintModal').classList.remove('is-open');
        document.body.classList.remove('print-single-student');
        document.body.style.overflow = '';
    }

    function executeSingleStudentPrint() {
        document.body.classList.add('print-single-student');
        window.print();
    }

    window.addEventListener('beforeprint', function () {
        const modal = document.getElementById('studentPrintModal');
        if (modal && modal.classList.contains('is-open')) {
            document.body.classList.add('print-single-student');
        } else {
            document.body.classList.remove('print-single-student');
        }
    });

    window.addEventListener('afterprint', function () {
        document.body.classList.remove('print-single-student');
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            const modal = document.getElementById('studentPrintModal');
            if (modal && modal.classList.contains('is-open')) {
                closeStudentPrintModal();
            }
        }
    });

    function fallbackCopyText(text) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.focus();
        textarea.select();
        try {
            document.execCommand('copy');
        } finally {
            document.body.removeChild(textarea);
        }
    }

    function copyStudentWhatsApp() {
        if (!activeStudentReport) return;

        const d = activeStudentReport;
        const teks =
`*LAPORAN KEHADIRAN SISWA*
*UPTD SDN 37 Sungai Bangek*
Periode: {{ $periodeMulaiText }} s.d. {{ $periodeSelesaiText }}

Nama Siswa : *${d.nama}*
NISN : ${d.nisn}
Kelas : ${d.kelas}

*Ringkasan Kehadiran:*
- Hadir : ${d.hadir} hari
- Terlambat : ${d.terlambat} hari
- Sakit : ${d.sakit} hari
- Izin : ${d.izin} hari
- Alpa : ${d.alpa} hari
- Total Catatan : ${d.total}

Terima kasih atas perhatian Bapak/Ibu Wali Murid.`;

        const showCopiedFeedback = function () {
            const btnWaLabel = document.getElementById('btnCopyWaLabel');
            if (btnWaLabel) {
                btnWaLabel.textContent = 'Tersalin ke Clipboard!';
                setTimeout(function () {
                    btnWaLabel.textContent = 'Salin Pesan WA';
                }, 2500);
            }
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(teks)
                .then(showCopiedFeedback)
                .catch(function () {
                    fallbackCopyText(teks);
                    showCopiedFeedback();
                });
        } else {
            fallbackCopyText(teks);
            showCopiedFeedback();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('quickSearchSiswa');
        const tbody = document.getElementById('reportTableBody');
        const emptySearchRow = document.getElementById('emptySearchReportRow');
        if (!searchInput || !tbody) return;

        const rows = Array.from(tbody.querySelectorAll('tr[data-report-row]'));
        if (!rows.length) return;

        searchInput.addEventListener('input', function () {
            const keyword = this.value.trim().toLowerCase();
            let visible = 0;

            rows.forEach(function (row) {
                const match = !keyword ||
                    row.dataset.nama.includes(keyword) ||
                    row.dataset.nisn.includes(keyword);

                if (match) {
                    row.style.display = '';
                    visible++;
                    const noCell = row.querySelector('.col-no');
                    if (noCell) noCell.textContent = visible;
                } else {
                    row.style.display = 'none';
                }
            });

            if (emptySearchRow) {
                emptySearchRow.style.display = visible === 0 ? '' : 'none';
            }
        });
    });
</script>
@endsection