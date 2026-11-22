@extends('layouts.app')

@section('title', 'Manajemen Pengguna - Absensi RFID')

@section('page-heading', 'Manajemen Pengguna')

@section('content')

@php
    $tanggalIndonesia = now()->locale('id')->translatedFormat('l, d F Y');
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
        padding: 18px 22px;
        margin-bottom: 20px;
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
        padding: 9px 14px;
        background: #fdf2f2;
        border: 1px solid #f5c6cb;
        color: #850000;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 700;
        white-space: nowrap;
    }

    .users-date svg {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }

    /* ================= TOMBOL ================= */
    .users-btn {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 6px;
        min-height: 36px;
        padding: 0 14px;
        border: 1px solid transparent;
        border-radius: 8px;
        background: #ffffff;
        color: #850000;
        font-family: inherit;
        font-size: 12.5px;
        font-weight: 700;
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
        min-height: 30px;
        padding: 5px 11px;
        border-radius: 6px;
        background: #ffffff;
        border-color: #e6caca;
        color: #850000;
        font-size: 12px;
        font-weight: 600;
        box-shadow: none;
    }

    .users-btn.edit:hover {
        background: #850000;
        border-color: #850000;
        color: #ffffff;
    }

    .users-btn.delete {
        min-height: 30px;
        padding: 5px 11px;
        border-radius: 6px;
        background: #ffffff;
        border-color: #fecaca;
        color: #b91c1c;
        font-size: 12px;
        font-weight: 600;
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
        margin-bottom: 16px;
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
        padding: 15px 20px;
        background: #850000;
        color: #ffffff;
    }

    .users-card-header-title h3 {
        margin: 0 0 3px;
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
        padding: 12px 16px;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        color: #374151;
        font-size: 12px;
        font-weight: 700;
        text-align: left;
        white-space: nowrap;
    }

    .users-table td {
        padding: 12px 16px;
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
        padding: 4px 10px;
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

    /* ================= PAGINATION ================= */
    .users-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        padding: 13px 18px;
        border-top: 1px solid #e5e7eb;
        background: #fafafa;
        font-size: 12px;
        color: #6b7280;
    }

    .users-pagination nav {
        display: flex;
        justify-content: flex-end;
    }

    .users-pagination nav > div:first-child {
        display: none;
    }

    .users-pagination nav > div:last-child {
        display: flex;
        align-items: center;
        gap: 4px;
        flex-wrap: wrap;
    }

    .users-pagination nav a,
    .users-pagination nav span[aria-current="page"] span,
    .users-pagination nav span[aria-disabled="true"] span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 30px;
        height: 30px;
        padding: 0 9px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        color: #374151;
        background: #ffffff;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
    }

    .users-pagination nav span[aria-current="page"] span {
        background: #850000;
        border-color: #850000;
        color: #ffffff;
    }

    .users-pagination nav a:hover {
        background: #fdf2f2;
        border-color: #850000;
        color: #850000;
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

        .users-card-header {
            padding: 14px 16px;
            flex-direction: column;
            align-items: flex-start;
        }

        .users-card-header-actions {
            width: 100%;
            justify-content: space-between;
        }

        /* Ubah baris tabel menjadi kartu rapi di layar HP agar tidak perlu geser samping */
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

        .users-btn {
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

    <section class="users-card">

        <div class="users-card-header">
            <div class="users-card-header-title">
                <h3>Daftar Akun Pengguna</h3>
                <p>Akun yang memiliki akses masuk ke sistem absensi</p>
            </div>

            <div class="users-card-header-actions">
                <span class="users-total-badge">
                    Total akun: {{ $users->total() }}
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

                <tbody>
                    @forelse ($users as $user)
                        @php
                            $roleClass = match ($user->role) {
                                'guru' => 'role-guru',
                                'wali_murid' => 'role-wali',
                                default => '',
                            };
                        @endphp
                        <tr>
                            <td class="users-col-no" data-label="No.">
                                {{ $users->firstItem() + $loop->index }}
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
                                @if ($user->role === 'wali_murid')
                                    {{ $user->anak?->nama_lengkap ?? 'Belum terhubung' }}
                                @elseif ($user->role === 'guru')
                                    {{ $user->kelasDiampu->pluck('nama_kelas')->join(', ') ?: 'Belum menjadi wali kelas' }}
                                @else
                                    —
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
                </tbody>
            </table>
        </div>

        <div class="users-pagination">
            <div>
                Menampilkan {{ $users->count() }} dari {{ $users->total() }} akun pengguna.
            </div>

            <div>
                {{ $users->links() }}
            </div>
        </div>

    </section>

</div>
@endsection