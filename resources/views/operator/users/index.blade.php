@extends('layouts.app')

@section('title', 'Manajemen Pengguna - Absensi RFID')

@section('page-heading', 'Manajemen Pengguna')

@section('content')

@php
    $tanggalIndonesia = now()->locale('id')->translatedFormat('l, d F Y');
    $isPaginated = method_exists($users, 'lastPage');
    $totalUsers = $isPaginated ? $users->total() : $users->count();
    $lastPage = $isPaginated ? $users->lastPage() : 1;
    $currentPage = $isPaginated ? $users->currentPage() : 1;
    $startNumber = $isPaginated ? ($users->firstItem() ?? 1) : 1;
@endphp

<style>
    .users-page {
        width: 100%;
        color: #1f2937;
        font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, sans-serif;
        font-size: 13px;
    }

    /* ================= HEADER HALAMAN ================= */
    .users-heading {
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

    .users-heading h2 {
        margin: 0 0 4px;
        color: #850000;
        font-family: inherit;
        font-size: 19px;
        font-weight: 700;
        letter-spacing: -0.015em;
    }

    .users-heading p {
        margin: 0;
        color: #6b7280;
        font-size: 13px;
        font-weight: 500;
    }

    .users-heading-right {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .users-date {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        background: #fdf2f2;
        border: 1px solid #f5c6cb;
        color: #850000;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
    }

    .users-date svg {
        width: 17px;
        height: 17px;
        flex-shrink: 0;
    }

    /* ================= FILTER & PENCARIAN ================= */
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

    .users-filters {
        display: grid;
        grid-template-columns: 210px 1fr 195px auto;
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

    .btn-users-reset {
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

    .btn-users-reset:hover {
        background: #fdf2f2;
        border-color: #850000;
        color: #850000;
    }

    /* ================= TOMBOL ================= */
    .users-btn {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border: 1px solid transparent;
        border-radius: 8px;
        background: #ffffff;
        color: #850000;
        font-family: inherit;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        white-space: nowrap;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: all 0.18s ease;
    }

    .users-btn:hover {
        background: #fdf2f2;
        color: #680000;
    }

    .users-btn.edit {
        padding: 5px 11px;
        border-radius: 6px;
        background: #ffffff;
        border-color: #e6caca;
        color: #850000;
        font-size: 12px;
        box-shadow: none;
    }

    .users-btn.edit:hover {
        background: #850000;
        border-color: #850000;
        color: #ffffff;
    }

    .users-btn.delete {
        padding: 5px 11px;
        border-radius: 6px;
        background: #ffffff;
        border-color: #fecaca;
        color: #b91c1c;
        font-size: 12px;
        box-shadow: none;
    }

    .users-btn.delete:hover {
        background: #b91c1c;
        border-color: #b91c1c;
        color: #ffffff;
    }

    /* ================= NOTIFIKASI ================= */
    .users-alert {
        padding: 12px 15px;
        margin-bottom: 14px;
        border: 1px solid;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
    }

    .users-alert.success {
        border-color: #bbf7d0;
        background: #f0fdf4;
        color: #166534;
    }

    .users-alert.error {
        border-color: #fecaca;
        background: #fef2f2;
        color: #991b1b;
    }

    .users-alert ul {
        margin: 6px 0 0 18px;
    }

    /* ================= KARTU & TABEL ================= */
    .users-card {
        overflow: hidden;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }

    .users-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding: 13px 20px;
        background: #850000;
        color: #ffffff;
    }

    .users-card-header-title h3 {
        margin: 0 0 2px;
        color: #ffffff;
        font-family: inherit;
        font-size: 15.5px;
        font-weight: 700;
    }

    .users-card-header-title p {
        margin: 0;
        color: #f3d1d1;
        font-size: 12px;
    }

    .users-card-header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .users-total-badge {
        padding: 6px 12px;
        border-radius: 7px;
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.22);
        color: #ffffff;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .users-table-wrap {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .users-table {
        width: 100%;
        border-collapse: collapse;
        font-family: inherit;
    }

    .users-table th {
        padding: 10px 16px;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        color: #374151;
        font-size: 12px;
        font-weight: 700;
        text-align: left;
        white-space: nowrap;
    }

    .users-table td {
        padding: 9px 16px;
        border-bottom: 1px solid #f3f4f6;
        color: #374151;
        font-size: 13px;
        vertical-align: middle;
    }

    .users-table tbody tr:hover {
        background: #fdf8f8;
    }

    .users-table tbody tr:last-child td {
        border-bottom: none;
    }

    .users-username {
        color: #111827;
        font-weight: 700;
    }

    .users-role {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 6px;
        background: #fdf2f2;
        border: 1px solid #f5c6cb;
        color: #850000;
        font-size: 11.5px;
        font-weight: 700;
        white-space: nowrap;
    }

    .users-role.role-guru {
        background: #dbeafe;
        border-color: #bfdbfe;
        color: #1d4ed8;
    }

    .users-role.role-wali {
        background: #dcfce7;
        border-color: #bbf7d0;
        color: #15803d;
    }

    .users-relation {
        color: #4b5563;
        line-height: 1.5;
    }

    .users-relation-empty {
        color: #9ca3af;
        font-style: italic;
        font-size: 12px;
    }

    .users-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
    }

    .users-actions form {
        margin: 0;
        display: inline-flex;
    }

    .users-empty {
        padding: 32px 16px !important;
        color: #6b7280 !important;
        text-align: center;
        font-size: 13px;
    }

    /* ================= FOOTER INFO ================= */
    .users-pagination {
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

    /* ================= RESPONSIVE TABLET ================= */
    @media (max-width: 960px) {
        .users-filters {
            grid-template-columns: 1fr 1fr;
        }
    }

    /* ================= RESPONSIVE MOBILE (TAMPILAN KARTU) ================= */
    @media (max-width: 768px) {
        .users-heading {
            padding: 16px;
            flex-direction: column;
            align-items: flex-start;
        }

        .users-heading h2 {
            font-size: 17px;
        }

        .users-date {
            font-size: 12px;
            padding: 7px 12px;
        }

        .users-filters {
            grid-template-columns: 1fr;
        }

        .users-card-header {
            padding: 14px 16px;
            flex-direction: column;
            align-items: flex-start;
        }

        .users-card-header-actions {
            width: 100%;
            justify-content: space-between;
        }

        .users-table thead {
            display: none;
        }

        .users-table,
        .users-table tbody,
        .users-table tr,
        .users-table td {
            display: block;
            width: 100%;
        }

        .users-table tbody tr {
            padding: 14px 16px;
            border-bottom: 1px solid #e5e7eb;
        }

        .users-table tbody tr:last-child {
            border-bottom: none;
        }

        .users-table td {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            padding: 6px 0;
            border-bottom: none;
            text-align: right;
        }

        .users-table td::before {
            content: attr(data-label);
            color: #6b7280;
            font-size: 12px;
            font-weight: 600;
            text-align: left;
            flex-shrink: 0;
        }

        .users-table td.users-col-no {
            display: none;
        }

        .users-table td.users-col-actions {
            margin-top: 8px;
            padding-top: 10px;
            border-top: 1px dashed #e5e7eb;
            justify-content: flex-end;
        }

        .users-table td.users-col-actions::before {
            display: none;
        }

        .users-actions {
            width: 100%;
            justify-content: flex-end;
        }

        .users-table td.users-empty {
            display: block;
            text-align: center;
            padding: 24px 12px !important;
        }

        .users-table td.users-empty::before {
            display: none;
        }

        .users-pagination {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    @media (max-width: 420px) {
        .users-card-header-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .users-card-header-actions .users-btn {
            width: 100%;
        }

        .users-actions .users-btn,
        .users-actions form {
            flex: 1;
        }

        .users-actions form .users-btn {
            width: 100%;
        }
    }
</style>

<div class="users-page">

    {{-- JUDUL DAN TANGGAL --}}
    <div class="users-heading">
        <div>
            <h2>Manajemen Pengguna</h2>
            <p>Kelola akun operator, guru, dan wali murid.</p>
        </div>

        <div class="users-heading-right">
            <div class="users-date">
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

    {{-- NOTIFIKASI --}}
    @if (session('success'))
        <div class="users-alert success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="users-alert error">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="users-alert error">
            <strong>Periksa kembali data:</strong>
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
            <h3 class="filter-title">Filter &amp; Pencarian Pengguna</h3>
            <span id="loadingAllBadge" class="loading-badge">Memuat seluruh akun pengguna...</span>
        </div>

        <div class="users-filters">
            <div class="filter-group">
                <label for="filterRole">Role Pengguna</label>
                <select id="filterRole">
                    <option value="">Semua Role</option>
                    <option value="operator">Operator</option>
                    <option value="guru">Guru</option>
                    <option value="wali_murid">Wali Murid</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="filterSearch">Cari Username / Kelas / Nama Anak</label>
                <input
                    type="text"
                    id="filterSearch"
                    placeholder="Ketik username, nama kelas, atau nama siswa...">
            </div>

            <div class="filter-group">
                <label for="filterHubungan">Status Hubungan</label>
                <select id="filterHubungan">
                    <option value="">Semua Status</option>
                    <option value="terhubung">Sudah Terhubung</option>
                    <option value="belum">Belum Terhubung</option>
                </select>
            </div>

            <div>
                <button type="button" id="btnResetFilter" class="btn-users-reset">
                    Reset
                </button>
            </div>
        </div>
    </section>

    {{-- KARTU & TABEL PENGGUNA --}}
    <section class="users-card">

        <div class="users-card-header">
            <div class="users-card-header-title">
                <h3 id="tableBannerTitle">Daftar Akun Pengguna</h3>
                <p id="tableBannerSubtitle">Akun yang memiliki akses masuk ke sistem absensi</p>
            </div>

            <div class="users-card-header-actions">
                <span id="usersTotalBadge" class="users-total-badge">
                    Total akun: {{ $totalUsers }}
                </span>

                <a href="{{ route('operator.users.create') }}" class="users-btn">
                    + Tambah User
                </a>
            </div>
        </div>

        <div class="users-table-wrap">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Hubungan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody id="usersTableBody">
                    @forelse ($users as $user)
                        @php
                            $roleClass = match ($user->role) {
                                'guru' => 'role-guru',
                                'wali_murid' => 'role-wali',
                                default => '',
                            };

                            if ($user->role === 'wali_murid') {
                                $hubunganText = $user->anak?->nama_lengkap ?? 'Belum terhubung';
                                $statusHubungan = $user->anak ? 'terhubung' : 'belum';
                            } elseif ($user->role === 'guru') {
                                $kelasList = $user->kelasDiampu->pluck('nama_kelas')->join(', ');
                                $hubunganText = $kelasList ?: 'Belum menjadi wali kelas';
                                $statusHubungan = $kelasList !== '' ? 'terhubung' : 'belum';
                            } else {
                                $hubunganText = '—';
                                $statusHubungan = 'terhubung';
                            }
                        @endphp

                        <tr data-user-row
                            data-role="{{ $user->role }}"
                            data-username="{{ strtolower($user->username) }}"
                            data-hubungan="{{ strtolower($hubunganText) }}"
                            data-status-hubungan="{{ $statusHubungan }}">
                            <td class="users-col-no col-no" data-label="No.">
                                {{ $startNumber + $loop->index }}
                            </td>

                            <td data-label="Username">
                                <span class="users-username">{{ $user->username }}</span>
                            </td>

                            <td data-label="Role">
                                <span class="users-role {{ $roleClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </td>

                            <td class="users-relation" data-label="Hubungan">
                                @if ($statusHubungan === 'belum')
                                    <span class="users-relation-empty">{{ $hubunganText }}</span>
                                @else
                                    {{ $hubunganText }}
                                @endif
                            </td>

                            <td class="users-col-actions" data-label="Aksi">
                                <div class="users-actions">
                                    <a class="users-btn edit"
                                       href="{{ route('operator.users.edit', $user) }}">
                                        ✎ Edit
                                    </a>

                                    @if ($user->id !== auth()->id())
                                        <form method="POST"
                                              action="{{ route('operator.users.destroy', $user) }}"
                                              onsubmit="return confirm('Yakin ingin menghapus akun ini?')">
                                            @csrf
                                            @method('DELETE')

                                            <button class="users-btn delete" type="submit">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="users-empty">
                                Belum ada pengguna.
                            </td>
                        </tr>
                    @endforelse

                    <tr id="emptyFilterRow" style="display: none;">
                        <td colspan="5" class="users-empty">
                            Tidak ada akun pengguna yang sesuai dengan filter.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="users-pagination">
            <div id="usersSummaryText">
                Menampilkan {{ $users->count() }} dari {{ $totalUsers }} akun pengguna.
            </div>
            <div>
                Gunakan filter role atau kolom pencarian di atas untuk menyaring akun pengguna.
            </div>
        </div>

    </section>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.getElementById('usersTableBody');
    const emptyRow = document.getElementById('emptyFilterRow');
    const filterRole = document.getElementById('filterRole');
    const filterSearch = document.getElementById('filterSearch');
    const filterHubungan = document.getElementById('filterHubungan');
    const btnReset = document.getElementById('btnResetFilter');
    const bannerTitle = document.getElementById('tableBannerTitle');
    const bannerSubtitle = document.getElementById('tableBannerSubtitle');
    const totalBadge = document.getElementById('usersTotalBadge');
    const summaryText = document.getElementById('usersSummaryText');
    const loadingBadge = document.getElementById('loadingAllBadge');

    const lastPage = {{ $lastPage }};
    const currentPage = {{ $currentPage }};

    const initialRows = Array.from(tbody.querySelectorAll('tr[data-user-row]'));
    if (!initialRows.length) return;

    const pagesMap = {};
    pagesMap[currentPage] = initialRows;

    let allRows = [...initialRows];

    const roleLabels = {
        operator: 'Operator',
        guru: 'Guru',
        wali_murid: 'Wali Murid'
    };

    function rebuildRoleOptions() {
        const previousValue = filterRole.value;
        const roleCounts = {
            operator: 0,
            guru: 0,
            wali_murid: 0
        };

        allRows.forEach(function (row) {
            const r = row.dataset.role;
            if (Object.prototype.hasOwnProperty.call(roleCounts, r)) {
                roleCounts[r]++;
            }
        });

        filterRole.innerHTML = `<option value="">Semua Role (${allRows.length} akun)</option>`;
        Object.keys(roleLabels).forEach(function (key) {
            const opt = document.createElement('option');
            opt.value = key;
            opt.textContent = `${roleLabels[key]} (${roleCounts[key]} akun)`;
            filterRole.appendChild(opt);
        });

        if (previousValue && Object.prototype.hasOwnProperty.call(roleLabels, previousValue)) {
            filterRole.value = previousValue;
        }
    }

    function applyFilter() {
        const selectedRole = filterRole.value;
        const keyword = filterSearch.value.trim().toLowerCase();
        const selectedHubungan = filterHubungan.value;

        let visibleCount = 0;

        allRows.forEach(function (row) {
            const matchRole = !selectedRole || row.dataset.role === selectedRole;
            const matchSearch = !keyword ||
                row.dataset.username.includes(keyword) ||
                row.dataset.hubungan.includes(keyword);
            const matchHubungan = !selectedHubungan || row.dataset.statusHubungan === selectedHubungan;

            if (matchRole && matchSearch && matchHubungan) {
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

        bannerTitle.textContent = selectedRole
            ? `Daftar Akun — ${roleLabels[selectedRole]}`
            : 'Daftar Akun Pengguna';

        bannerSubtitle.textContent = selectedRole
            ? `Menampilkan seluruh ${visibleCount} akun dengan role ${roleLabels[selectedRole]}`
            : `Menampilkan ${visibleCount} dari ${allRows.length} akun pengguna terdaftar`;

        if (totalBadge) {
            totalBadge.textContent = `Total akun: ${allRows.length}`;
        }

        summaryText.textContent = `Menampilkan ${visibleCount} dari ${allRows.length} akun pengguna.`;
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
                        doc.querySelectorAll('#usersTableBody tr[data-user-row]')
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

        rebuildRoleOptions();
        applyFilter();
    }

    filterRole.addEventListener('change', applyFilter);
    filterSearch.addEventListener('input', applyFilter);
    filterHubungan.addEventListener('change', applyFilter);

    btnReset.addEventListener('click', function () {
        filterRole.value = '';
        filterSearch.value = '';
        filterHubungan.value = '';
        applyFilter();
    });

    rebuildRoleOptions();
    applyFilter();
    fetchRemainingPages();
});
</script>

@endsection