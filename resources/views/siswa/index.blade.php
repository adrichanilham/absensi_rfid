@extends('layouts.app')

@section('title', 'Data Siswa - Absensi RFID')

@section('page-heading', 'Data Siswa')

@section('content')

@php
    $tanggalIndonesia = now()->locale('id')->translatedFormat('l, d F Y');
    $isGuru = auth()->user()->role === 'guru';
    $isPaginated = method_exists($siswa, 'lastPage');
    $totalSiswa = $isPaginated ? $siswa->total() : $siswa->count();
    $lastPage = $isPaginated ? $siswa->lastPage() : 1;
    $currentPage = $isPaginated ? $siswa->currentPage() : 1;
    $startNumber = $isPaginated ? ($siswa->firstItem() ?? 1) : 1;
@endphp

<style>
    .student-page {
        width: 100%;
        font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, sans-serif;
        color: #1f2937;
    }

    /* HEADER HALAMAN */
    .student-heading {
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

    .student-heading h2 {
        margin: 0 0 4px;
        color: #850000;
        font-family: inherit;
        font-size: 19px;
        font-weight: 700;
        letter-spacing: -0.015em;
    }

    .student-heading p {
        margin: 0;
        color: #6b7280;
        font-family: inherit;
        font-size: 13px;
        font-weight: 500;
    }

    .student-date {
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

    .student-date svg {
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

    .student-filters {
        display: grid;
        grid-template-columns: 230px 1fr 165px auto;
        gap: 12px;
        align-items: end;
    }

    .student-filters.guru-filters {
        grid-template-columns: 1fr 180px auto;
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

    .btn-student-reset {
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

    .btn-student-reset:hover {
        background: #fdf2f2;
        border-color: #850000;
        color: #850000;
    }

    /* NOTIFIKASI */
    .student-alert {
        padding: 12px 15px;
        margin-bottom: 14px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
    }

    .student-alert.success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
    }

    .student-alert.error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }

    .student-alert ul {
        margin: 6px 0 0;
        padding-left: 18px;
    }

    /* TABEL PANEL */
    .student-table-panel {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }

    .student-table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding: 13px 20px;
        background: #850000;
        color: #ffffff;
    }

    .student-table-header h3 {
        margin: 0 0 2px;
        font-family: inherit;
        font-size: 15.5px;
        font-weight: 700;
        color: #ffffff;
    }

    .student-table-header p {
        margin: 0;
        color: #f3d1d1;
        font-size: 12px;
    }

    /* TOMBOL */
    .student-btn {
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
        transition: all 0.18s ease;
        white-space: nowrap;
    }

    .student-btn-add {
        background: #ffffff;
        color: #850000;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .student-btn-add:hover {
        background: #fdf2f2;
    }

    .student-btn-edit {
        padding: 5px 11px;
        border-radius: 6px;
        font-size: 12px;
        background: #ffffff;
        color: #850000;
        border-color: #e6caca;
    }

    .student-btn-edit:hover {
        background: #850000;
        color: #ffffff;
        border-color: #850000;
    }

    .student-btn-delete {
        padding: 5px 11px;
        border-radius: 6px;
        font-size: 12px;
        background: #ffffff;
        color: #b91c1c;
        border-color: #fecaca;
    }

    .student-btn-delete:hover {
        background: #b91c1c;
        color: #ffffff;
        border-color: #b91c1c;
    }

    /* TABEL */
    .student-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .student-table {
        width: 100%;
        min-width: 850px;
        border-collapse: collapse;
        font-family: inherit;
    }

    .student-table thead th {
        padding: 10px 14px;
        background: #f9fafb;
        color: #374151;
        font-size: 12px;
        font-weight: 700;
        text-align: left;
        white-space: nowrap;
        border-bottom: 1px solid #e5e7eb;
    }

    .student-table tbody td {
        padding: 9px 14px;
        border-bottom: 1px solid #f3f4f6;
        color: #374151;
        font-size: 13px;
        vertical-align: middle;
    }

    .student-table tbody tr:hover {
        background: #fdf8f8;
    }

    .student-table tbody tr:last-child td {
        border-bottom: none;
    }

    .student-name {
        color: #111827;
        font-weight: 600;
    }

    .student-uid-empty {
        color: #9ca3af;
        font-style: italic;
        font-size: 12px;
    }

    .student-actions {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .student-actions form {
        display: inline-flex;
        margin: 0;
    }

    /* BADGE STATUS */
    .student-status {
        display: inline-block;
        min-width: 64px;
        padding: 3px 10px;
        border-radius: 6px;
        text-align: center;
        font-size: 11.5px;
        font-weight: 700;
    }

    .student-status.active {
        background: #dcfce7;
        color: #15803d;
    }

    .student-status.inactive {
        background: #fee2e2;
        color: #b91c1c;
    }

    .student-empty {
        padding: 32px 16px !important;
        text-align: center;
        color: #6b7280 !important;
        font-size: 13px !important;
    }

    /* FOOTER INFO */
    .student-pagination {
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

    @media (max-width: 960px) {
        .student-filters,
        .student-filters.guru-filters {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 600px) {
        .student-heading {
            padding: 16px;
            flex-direction: column;
            align-items: flex-start;
        }

        .student-heading h2 {
            font-size: 17px;
        }

        .student-date {
            font-size: 12px;
            padding: 7px 12px;
        }

        .student-filters,
        .student-filters.guru-filters {
            grid-template-columns: 1fr;
        }

        .student-table-header {
            align-items: flex-start;
        }

        .student-pagination {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="student-page">

    <div class="student-heading">
        <div>
            <h2>Data Siswa</h2>
            <p>
                {{ $isGuru
                    ? 'Daftar siswa pada kelas yang Anda ampu — SDN 37 Sungai Bangek.'
                    : 'Pengelolaan data siswa SDN 37 Sungai Bangek.' }}
            </p>
        </div>

        <div class="student-date">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            <span>{{ $tanggalIndonesia }}</span>
        </div>
    </div>

    @if (session('success'))
        <div class="student-alert success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="student-alert error">
            <strong>Periksa kembali data berikut:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="filter-panel">
        <div class="filter-header-row">
            <h3 class="filter-title">Filter &amp; Pencarian Data Siswa</h3>
            <span id="loadingAllBadge" class="loading-badge">Memuat seluruh data siswa...</span>
        </div>

        <div class="student-filters {{ $isGuru ? 'guru-filters' : '' }}">
            @unless ($isGuru)
                <div class="filter-group">
                    <label for="filterKelas">Kelas</label>
                    <select id="filterKelas">
                        <option value="">Semua Kelas</option>
                    </select>
                </div>
            @endunless

            <div class="filter-group">
                <label for="filterSearch">Cari Nama / NISN</label>
                <input
                    type="text"
                    id="filterSearch"
                    placeholder="Ketik nama siswa atau NISN...">
            </div>

            <div class="filter-group">
                <label for="filterStatus">Status</label>
                <select id="filterStatus">
                    <option value="">Semua status</option>
                    <option value="aktif">Aktif</option>
                    <option value="tidak_aktif">Tidak Aktif</option>
                </select>
            </div>

            <div>
                <button type="button" id="btnResetFilter" class="btn-student-reset">
                    Reset
                </button>
            </div>
        </div>
    </section>

    <section class="student-table-panel">

        <div class="student-table-header">
            <div>
                <h3 id="tableBannerTitle">Daftar Siswa</h3>
                <p id="tableBannerSubtitle">
                    {{ $isGuru ? 'Data siswa kelas yang Anda ampu' : 'Data siswa SDN 37 Sungai Bangek' }}
                </p>
            </div>

            @unless ($isGuru)
                <a href="{{ route('siswa.create') }}"
                   class="student-btn student-btn-add">
                    + Tambah Siswa
                </a>
            @endunless
        </div>

        <div class="student-table-wrapper">
            <table class="student-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>NISN</th>
                        <th>Nama Lengkap</th>
                        <th>Jenis Kelamin</th>
                        <th>Kelas</th>
                        <th>UID RFID</th>
                        <th>Status</th>

                        @unless ($isGuru)
                            <th>Aksi</th>
                        @endunless
                    </tr>
                </thead>

                <tbody id="siswaTableBody">
                    @forelse ($siswa as $item)
                        <tr data-siswa-row
                            data-kelas="{{ $item->kelas?->nama_kelas ?? '' }}"
                            data-nama="{{ strtolower($item->nama_lengkap) }}"
                            data-nisn="{{ strtolower($item->nisn) }}"
                            data-status="{{ $item->status }}">
                            <td class="col-no">
                                {{ $startNumber + $loop->index }}
                            </td>

                            <td>{{ $item->nisn }}</td>

                            <td>
                                <span class="student-name">
                                    {{ $item->nama_lengkap }}
                                </span>
                            </td>

                            <td>
                                {{ $item->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </td>

                            <td>
                                {{ $item->kelas?->nama_kelas ?? '-' }}
                            </td>

                            <td>
                                @if ($item->rfid?->uid)
                                    {{ $item->rfid->uid }}
                                @else
                                    <span class="student-uid-empty">Belum terdaftar</span>
                                @endif
                            </td>

                            <td>
                                <span class="student-status {{ $item->status === 'aktif' ? 'active' : 'inactive' }}">
                                    {{ $item->status === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </td>

                            @unless ($isGuru)
                                <td>
                                    <div class="student-actions">
                                        <a href="{{ route('siswa.edit', $item) }}"
                                           class="student-btn student-btn-edit">
                                            ✎ Edit
                                        </a>

                                        @if ($item->status === 'aktif')
                                            <form
                                                action="{{ route('siswa.destroy', $item) }}"
                                                method="POST"
                                                onsubmit="return confirm('Yakin ingin menonaktifkan siswa ini?')"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="student-btn student-btn-delete">
                                                    Nonaktifkan
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            @endunless
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isGuru ? 7 : 8 }}"
                                class="student-empty">
                                Belum ada data siswa.
                            </td>
                        </tr>
                    @endforelse

                    <tr id="emptyFilterRow" style="display: none;">
                        <td colspan="{{ $isGuru ? 7 : 8 }}"
                            class="student-empty">
                            Tidak ada data siswa yang sesuai dengan filter.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="student-pagination">
            <div id="siswaSummaryText">
                Menampilkan {{ $siswa->count() }} dari {{ $totalSiswa }} data siswa.
            </div>
            <div>
                {{ $isGuru
                    ? 'Gunakan kolom pencarian atau filter status untuk menyaring siswa.'
                    : 'Gunakan filter kelas atau kolom pencarian di atas untuk menyaring siswa.' }}
            </div>
        </div>

    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const isGuru = {{ $isGuru ? 'true' : 'false' }};
    const tbody = document.getElementById('siswaTableBody');
    const emptyRow = document.getElementById('emptyFilterRow');
    const filterKelas = document.getElementById('filterKelas');
    const filterSearch = document.getElementById('filterSearch');
    const filterStatus = document.getElementById('filterStatus');
    const btnReset = document.getElementById('btnResetFilter');
    const bannerTitle = document.getElementById('tableBannerTitle');
    const bannerSubtitle = document.getElementById('tableBannerSubtitle');
    const summaryText = document.getElementById('siswaSummaryText');
    const loadingBadge = document.getElementById('loadingAllBadge');

    const lastPage = {{ $lastPage }};
    const currentPage = {{ $currentPage }};

    const initialRows = Array.from(tbody.querySelectorAll('tr[data-siswa-row]'));
    if (!initialRows.length) return;

    const pagesMap = {};
    pagesMap[currentPage] = initialRows;

    let allRows = [...initialRows];

    function getGuruClassLabel() {
        const classes = [...new Set(
            allRows.map(r => r.dataset.kelas).filter(Boolean)
        )];
        return classes.length === 1 ? classes[0] : 'Kelas Diampu';
    }

    function rebuildClassOptions() {
        if (isGuru || !filterKelas) return;

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

        filterKelas.innerHTML = `<option value="">Semua Kelas (${allRows.length} siswa)</option>`;
        sortedClasses.forEach(function (kls) {
            const opt = document.createElement('option');
            opt.value = kls;
            opt.textContent = `${kls} (${classCounts[kls]} siswa)`;
            filterKelas.appendChild(opt);
        });

        if (previousValue && classCounts[previousValue]) {
            filterKelas.value = previousValue;
        }
    }

    function applyFilter() {
        const selectedKelas = (!isGuru && filterKelas) ? filterKelas.value : '';
        const keyword = filterSearch.value.trim().toLowerCase();
        const selectedStatus = filterStatus.value;

        let visibleCount = 0;

        allRows.forEach(function (row) {
            const matchKelas = !selectedKelas || row.dataset.kelas === selectedKelas;
            const matchSearch = !keyword ||
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

        if (isGuru) {
            const guruKelasLabel = getGuruClassLabel();
            bannerTitle.textContent = `Daftar Siswa — ${guruKelasLabel}`;
            bannerSubtitle.textContent = `Menampilkan ${visibleCount} dari ${allRows.length} siswa pada ${guruKelasLabel}`;
        } else {
            bannerTitle.textContent = selectedKelas
                ? `Daftar Siswa — ${selectedKelas}`
                : 'Daftar Siswa';

            bannerSubtitle.textContent = selectedKelas
                ? `Menampilkan seluruh ${visibleCount} siswa pada ${selectedKelas}`
                : `Menampilkan ${visibleCount} dari ${allRows.length} siswa terdaftar`;
        }

        summaryText.textContent = `Menampilkan ${visibleCount} dari ${allRows.length} data siswa.`;
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
                        doc.querySelectorAll('#siswaTableBody tr[data-siswa-row]')
                    );
                } catch (e) {
                    // Abaikan jika ada halaman gagal dimuat
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

    if (filterKelas) {
        filterKelas.addEventListener('change', applyFilter);
    }
    filterSearch.addEventListener('input', applyFilter);
    filterStatus.addEventListener('change', applyFilter);

    btnReset.addEventListener('click', function () {
        if (filterKelas) filterKelas.value = '';
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