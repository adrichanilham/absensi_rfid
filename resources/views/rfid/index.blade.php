@extends('layouts.app')

@section('title', 'Data RFID - Absensi RFID')

@section('page-heading', 'Data RFID')

@section('content')

@php
    $tanggalIndonesia = now()->locale('id')->translatedFormat('l, d F Y');
    $isPaginated = method_exists($rfid, 'lastPage');
    $totalKartu = $isPaginated ? $rfid->total() : $rfid->count();
    $lastPage = $isPaginated ? $rfid->lastPage() : 1;
    $currentPage = $isPaginated ? $rfid->currentPage() : 1;
    $startNumber = $isPaginated ? ($rfid->firstItem() ?? 1) : 1;
@endphp

<style>
    .rfid-page {
        width: 100%;
        font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, sans-serif;
        color: #1f2937;
    }

    /* HEADER HALAMAN */
    .rfid-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 14px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
    }

    .rfid-heading h2 {
        margin: 0 0 4px;
        color: #850000;
        font-family: inherit;
        font-size: 19px;
        font-weight: 700;
        letter-spacing: -0.015em;
    }

    .rfid-heading p {
        margin: 0;
        color: #6b7280;
        font-family: inherit;
        font-size: 13px;
        font-weight: 500;
    }

    .rfid-date {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        background: #fdf2f2;
        border: 1px solid #f5c6cb;
        color: #850000;
        border-radius: 8px;
        font-family: inherit;
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
    }

    .rfid-date svg {
        width: 17px;
        height: 17px;
        flex-shrink: 0;
    }

    /* FILTER & PENCARIAN */
    .filter-panel {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 14px 18px;
        margin-bottom: 14px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
    }

    .filter-header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
    }

    .filter-title {
        margin: 0;
        color: #850000;
        font-size: 14.5px;
        font-weight: 700;
    }

    .loading-badge {
        display: none;
        font-size: 11.5px;
        font-weight: 600;
        color: #850000;
        background: #fdf2f2;
        border: 1px solid #f5c6cb;
        padding: 3px 10px;
        border-radius: 999px;
    }

    .rfid-filters {
        display: grid;
        grid-template-columns: 230px 1fr 165px auto;
        gap: 12px;
        align-items: end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .filter-group label {
        font-size: 12px;
        font-weight: 600;
        color: #374151;
    }

    .filter-group select,
    .filter-group input {
        height: 36px;
        padding: 0 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-family: inherit;
        font-size: 13px;
        color: #1f2937;
        background: #ffffff;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
    }

    .filter-group select:focus,
    .filter-group input:focus {
        border-color: #850000;
        box-shadow: 0 0 0 3px rgba(133, 0, 0, 0.1);
    }

    .btn-rfid-reset {
        height: 36px;
        padding: 0 15px;
        background: #ffffff;
        color: #374151;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-family: inherit;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
        transition: all 0.15s;
    }

    .btn-rfid-reset:hover {
        background: #fdf2f2;
        border-color: #850000;
        color: #850000;
    }

    /* NOTIFIKASI */
    .rfid-alert {
        padding: 12px 15px;
        margin-bottom: 14px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
    }

    .rfid-alert.success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
    }

    .rfid-alert.error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }

    .rfid-alert ul {
        margin: 6px 0 0;
        padding-left: 18px;
    }

    /* PANEL TABEL */
    .rfid-table-panel {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }

    .rfid-table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding: 13px 20px;
        background: #850000;
        color: #ffffff;
    }

    .rfid-table-header h3 {
        margin: 0 0 2px;
        font-family: inherit;
        font-size: 15.5px;
        font-weight: 700;
        color: #ffffff;
    }

    .rfid-table-header p {
        margin: 0;
        color: #f3d1d1;
        font-size: 12px;
    }

    /* TOMBOL */
    .rfid-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px 14px;
        border: 1px solid transparent;
        border-radius: 8px;
        text-decoration: none;
        font-family: inherit;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.18s ease;
    }

    .rfid-btn-add {
        background: #ffffff;
        color: #850000;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .rfid-btn-add:hover {
        background: #fdf2f2;
    }

    .rfid-btn-edit {
        padding: 5px 11px;
        border-radius: 6px;
        font-size: 12px;
        background: #ffffff;
        color: #850000;
        border-color: #e6caca;
    }

    .rfid-btn-edit:hover {
        background: #850000;
        color: #ffffff;
        border-color: #850000;
    }

    .rfid-btn-delete {
        padding: 5px 11px;
        border-radius: 6px;
        font-size: 12px;
        background: #ffffff;
        color: #b91c1c;
        border-color: #fecaca;
    }

    .rfid-btn-delete:hover {
        background: #b91c1c;
        color: #ffffff;
        border-color: #b91c1c;
    }

    /* TABEL */
    .rfid-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .rfid-table {
        width: 100%;
        min-width: 820px;
        border-collapse: collapse;
        font-family: inherit;
    }

    .rfid-table thead th {
        padding: 10px 14px;
        background: #f9fafb;
        color: #374151;
        font-size: 12px;
        font-weight: 700;
        text-align: left;
        white-space: nowrap;
        border-bottom: 1px solid #e5e7eb;
    }

    .rfid-table tbody td {
        padding: 9px 14px;
        border-bottom: 1px solid #f3f4f6;
        color: #374151;
        font-size: 13px;
        vertical-align: middle;
    }

    .rfid-table tbody tr:hover {
        background: #fdf8f8;
    }

    .rfid-table tbody tr:last-child td {
        border-bottom: none;
    }

    .rfid-uid {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 6px;
        background: #fdf2f2;
        border: 1px solid #f5c6cb;
        color: #850000;
        font-weight: 700;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 12.5px;
        letter-spacing: 0.03em;
    }

    .rfid-student-name {
        color: #111827;
        font-weight: 600;
    }

    /* BADGE STATUS */
    .rfid-status {
        display: inline-block;
        min-width: 64px;
        padding: 3px 10px;
        border-radius: 6px;
        text-align: center;
        font-size: 11.5px;
        font-weight: 700;
    }

    .rfid-status.active {
        background: #dcfce7;
        color: #15803d;
    }

    .rfid-status.inactive {
        background: #fee2e2;
        color: #b91c1c;
    }

    /* AKSI */
    .rfid-actions {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .rfid-actions form {
        display: inline-flex;
        margin: 0;
    }

    .rfid-empty {
        padding: 32px 16px !important;
        text-align: center;
        color: #6b7280 !important;
        font-size: 13px !important;
    }

    /* FOOTER INFO */
    .rfid-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        padding: 11px 18px;
        border-top: 1px solid #e5e7eb;
        background: #fafafa;
        font-size: 12px;
        color: #6b7280;
    }

    /* RESPONSIVE */
    @media (max-width: 960px) {
        .rfid-filters {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 600px) {
        .rfid-heading {
            padding: 16px;
            flex-direction: column;
            align-items: flex-start;
        }

        .rfid-heading h2 {
            font-size: 17px;
        }

        .rfid-date {
            font-size: 12px;
            padding: 7px 12px;
        }

        .rfid-filters {
            grid-template-columns: 1fr;
        }

        .rfid-table-header {
            align-items: flex-start;
        }

        .rfid-pagination {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="rfid-page">

    {{-- JUDUL DAN TANGGAL --}}
    <div class="rfid-heading">
        <div>
            <h2>Data RFID</h2>
            <p>Kelola kartu RFID yang terhubung dengan data siswa.</p>
        </div>

        <div class="rfid-date">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            <span>{{ $tanggalIndonesia }}</span>
        </div>
    </div>

    {{-- NOTIFIKASI --}}
    @if (session('success'))
        <div class="rfid-alert success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rfid-alert error">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rfid-alert error">
            <strong>Periksa kembali data berikut:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FILTER & PENCARIAN --}}
    <section class="filter-panel">
        <div class="filter-header-row">
            <h3 class="filter-title">Filter &amp; Pencarian Kartu RFID</h3>
            <span id="loadingAllBadge" class="loading-badge">Memuat seluruh data kartu...</span>
        </div>

        <div class="rfid-filters">
            <div class="filter-group">
                <label for="filterKelas">Kelas</label>
                <select id="filterKelas">
                    <option value="">Semua Kelas</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="filterSearch">Cari UID / Nama / NISN</label>
                <input
                    type="text"
                    id="filterSearch"
                    placeholder="Ketik UID kartu, nama siswa, atau NISN...">
            </div>

            <div class="filter-group">
                <label for="filterStatus">Status Kartu</label>
                <select id="filterStatus">
                    <option value="">Semua status</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>

            <div>
                <button type="button" id="btnResetFilter" class="btn-rfid-reset">
                    Reset
                </button>
            </div>
        </div>
    </section>

    {{-- TABEL DATA RFID --}}
    <section class="rfid-table-panel">

        <div class="rfid-table-header">
            <div>
                <h3 id="tableBannerTitle">Daftar Kartu RFID</h3>
                <p id="tableBannerSubtitle">Informasi UID kartu dan siswa yang terdaftar</p>
            </div>

            <a href="{{ route('rfid.create') }}"
               class="rfid-btn rfid-btn-add">
                + Daftarkan Kartu
            </a>
        </div>

        <div class="rfid-table-wrapper">
            <table class="rfid-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>UID RFID</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Status Kartu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody id="rfidTableBody">
                    @forelse ($rfid as $kartu)
                        <tr data-rfid-row
                            data-kelas="{{ $kartu->siswa?->kelas?->nama_kelas ?? '' }}"
                            data-uid="{{ strtolower($kartu->uid) }}"
                            data-nama="{{ strtolower($kartu->siswa?->nama_lengkap ?? '') }}"
                            data-nisn="{{ strtolower($kartu->siswa?->nisn ?? '') }}"
                            data-status="{{ $kartu->aktif ? 'aktif' : 'nonaktif' }}">
                            <td class="col-no">
                                {{ $startNumber + $loop->index }}
                            </td>

                            <td>
                                <span class="rfid-uid">
                                    {{ $kartu->uid }}
                                </span>
                            </td>

                            <td>
                                {{ $kartu->siswa?->nisn ?? '-' }}
                            </td>

                            <td>
                                <span class="rfid-student-name">
                                    {{ $kartu->siswa?->nama_lengkap ?? 'Siswa tidak ditemukan' }}
                                </span>
                            </td>

                            <td>
                                {{ $kartu->siswa?->kelas?->nama_kelas ?? '-' }}
                            </td>

                            <td>
                                @if ($kartu->aktif)
                                    <span class="rfid-status active">
                                        Aktif
                                    </span>
                                @else
                                    <span class="rfid-status inactive">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td>
                                <div class="rfid-actions">
                                    <a href="{{ route('rfid.edit', $kartu) }}"
                                       class="rfid-btn rfid-btn-edit">
                                        ✎ Edit
                                    </a>

                                    @if ($kartu->aktif)
                                        <form
                                            action="{{ route('rfid.destroy', $kartu) }}"
                                            method="POST"
                                            onsubmit="return confirm('Nonaktifkan kartu RFID ini?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="rfid-btn rfid-btn-delete">
                                                Nonaktifkan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="rfid-empty">
                                Belum ada kartu RFID yang terdaftar.
                            </td>
                        </tr>
                    @endforelse

                    <tr id="emptyFilterRow" style="display: none;">
                        <td colspan="7" class="rfid-empty">
                            Tidak ada kartu RFID yang sesuai dengan filter.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- FOOTER INFO --}}
        <div class="rfid-pagination">
            <div id="rfidSummaryText">
                Menampilkan {{ $rfid->count() }} dari {{ $totalKartu }} kartu RFID.
            </div>
            <div>
                Gunakan filter kelas atau kolom pencarian di atas untuk menyaring kartu RFID.
            </div>
        </div>

    </section>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.getElementById('rfidTableBody');
    const emptyRow = document.getElementById('emptyFilterRow');
    const filterKelas = document.getElementById('filterKelas');
    const filterSearch = document.getElementById('filterSearch');
    const filterStatus = document.getElementById('filterStatus');
    const btnReset = document.getElementById('btnResetFilter');
    const bannerTitle = document.getElementById('tableBannerTitle');
    const bannerSubtitle = document.getElementById('tableBannerSubtitle');
    const summaryText = document.getElementById('rfidSummaryText');
    const loadingBadge = document.getElementById('loadingAllBadge');

    const lastPage = {{ $lastPage }};
    const currentPage = {{ $currentPage }};

    const initialRows = Array.from(tbody.querySelectorAll('tr[data-rfid-row]'));
    if (!initialRows.length) return;

    const pagesMap = {};
    pagesMap[currentPage] = initialRows;

    let allRows = [...initialRows];

    function rebuildClassOptions() {
        const previousValue = filterKelas.value;
        const classCounts = {};

        allRows.forEach(function (row) {
            const kls = row.dataset.kelas;
            if (kls) {
                classCounts[kls] = (classCounts[kls] || 0) + 1;
            }
        });

        const sortedClasses = Object.keys(classCounts).sort(function (a, b) {
            return a.localeCompare(b, 'id', { numeric: true });
        });

        filterKelas.innerHTML = `<option value="">Semua Kelas (${allRows.length} kartu)</option>`;
        sortedClasses.forEach(function (kls) {
            const opt = document.createElement('option');
            opt.value = kls;
            opt.textContent = `${kls} (${classCounts[kls]} kartu)`;
            filterKelas.appendChild(opt);
        });

        if (previousValue && classCounts[previousValue]) {
            filterKelas.value = previousValue;
        }
    }

    function applyFilter() {
        const selectedKelas = filterKelas.value;
        const keyword = filterSearch.value.trim().toLowerCase();
        const selectedStatus = filterStatus.value;

        let visibleCount = 0;

        allRows.forEach(function (row) {
            const matchKelas = !selectedKelas || row.dataset.kelas === selectedKelas;
            const matchSearch = !keyword ||
                row.dataset.uid.includes(keyword) ||
                row.dataset.nama.includes(keyword) ||
                row.dataset.nisn.includes(keyword);
            const matchStatus = !selectedStatus || row.dataset.status === selectedStatus;

            if (matchKelas && matchSearch && matchStatus) {
                row.style.display = '';
                visibleCount++;
                const noCell = row.querySelector('.col-no');
                if (noCell) noCell.textContent = visibleCount;
            } else {
                row.style.display = 'none';
            }
        });

        if (emptyRow) {
            emptyRow.style.display = visibleCount === 0 ? '' : 'none';
        }

        bannerTitle.textContent = selectedKelas
            ? `Daftar Kartu RFID — ${selectedKelas}`
            : 'Daftar Kartu RFID';

        bannerSubtitle.textContent = selectedKelas
            ? `Menampilkan seluruh ${visibleCount} kartu RFID pada ${selectedKelas}`
            : `Menampilkan ${visibleCount} dari ${allRows.length} kartu RFID terdaftar`;

        summaryText.textContent = `Menampilkan ${visibleCount} dari ${allRows.length} kartu RFID.`;
    }

    async function fetchRemainingPages() {
        if (lastPage <= 1) return;

        if (loadingBadge) {
            loadingBadge.style.display = 'inline-block';
        }

        const pagesToFetch = [];
        for (let p = 1; p <= lastPage; p++) {
            if (p !== currentPage) {
                pagesToFetch.push(p);
            }
        }

        const batchSize = 3;
        for (let i = 0; i < pagesToFetch.length; i += batchSize) {
            const batch = pagesToFetch.slice(i, i + batchSize);

            await Promise.all(batch.map(async function (pageNum) {
                try {
                    const url = new URL(window.location.href);
                    url.searchParams.set('page', pageNum);

                    const res = await fetch(url.toString(), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (!res.ok) return;

                    const html = await res.text();
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    pagesMap[pageNum] = Array.from(
                        doc.querySelectorAll('#rfidTableBody tr[data-rfid-row]')
                    );
                } catch (e) {
                }
            }));
        }

        allRows = [];
        for (let p = 1; p <= lastPage; p++) {
            if (pagesMap[p]) {
                allRows.push(...pagesMap[p]);
            }
        }

        allRows.forEach(function (row) {
            if (!tbody.contains(row)) {
                tbody.insertBefore(row, emptyRow);
            }
        });

        if (loadingBadge) {
            loadingBadge.style.display = 'none';
        }

        rebuildClassOptions();
        applyFilter();
    }

    filterKelas.addEventListener('change', applyFilter);
    filterSearch.addEventListener('input', applyFilter);
    filterStatus.addEventListener('change', applyFilter);

    btnReset.addEventListener('click', function () {
        filterKelas.value = '';
        filterSearch.value = '';
        filterStatus.value = '';
        applyFilter();
    });

    rebuildClassOptions();
    applyFilter();
    fetchRemainingPages();
});
</script>

@endsection