@extends('layouts.app')

@section('title', 'Data Siswa - Absensi RFID')

@section('page-heading', 'Data Siswa')

@section('content')

@php
    $tanggalIndonesia = now()->locale('id')->translatedFormat('l, d F Y');
@endphp

<style>
    .student-page {
        width: 100%;
        font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, sans-serif;
        color: #1f2937;
    }

    .student-heading {
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
        padding: 9px 15px;
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

    .student-alert {
        padding: 12px 15px;
        margin-bottom: 16px;
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
        padding: 15px 20px;
        background: #850000;
        color: #ffffff;
    }

    .student-table-header h3 {
        margin: 0 0 3px;
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
        padding: 6px 12px;
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
        padding: 6px 12px;
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
        padding: 12px 14px;
        background: #f9fafb;
        color: #374151;
        font-size: 12px;
        font-weight: 700;
        text-align: left;
        white-space: nowrap;
        border-bottom: 1px solid #e5e7eb;
    }

    .student-table tbody td {
        padding: 12px 14px;
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

    .student-status {
        display: inline-block;
        min-width: 64px;
        padding: 4px 10px;
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

    .student-pagination {
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

    .student-pagination nav {
        display: flex;
        justify-content: flex-end;
    }

    .student-pagination nav > div:first-child {
        display: none;
    }

    .student-pagination nav > div:last-child {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .student-pagination nav a,
    .student-pagination nav span[aria-current="page"] span,
    .student-pagination nav span[aria-disabled="true"] span {
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

    .student-pagination nav span[aria-current="page"] span {
        background: #850000;
        border-color: #850000;
        color: #ffffff;
    }

    .student-pagination nav a:hover {
        background: #fdf2f2;
        border-color: #850000;
        color: #850000;
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
            <p>Pengelolaan data siswa SDN 37 Sungai Bangek.</p>
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

    <section class="student-table-panel">

        <div class="student-table-header">
            <div>
                <h3>Daftar Siswa</h3>
                <p>Data siswa SDN 37 Sungai Bangek</p>
            </div>

            @if(auth()->user()->role !== 'guru')
                <a href="{{ route('siswa.create') }}"
                   class="student-btn student-btn-add">
                    + Tambah Siswa
                </a>
            @endif
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

                        @if(auth()->user()->role !== 'guru')
                            <th>Aksi</th>
                        @endif
                    </tr>
                </thead>

                <tbody>
                    @forelse ($siswa as $item)
                        <tr>
                            <td>
                                {{ $siswa->firstItem() + $loop->index }}
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

                            @if(auth()->user()->role !== 'guru')
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
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role !== 'guru' ? 8 : 7 }}"
                                class="student-empty">
                                Belum ada data siswa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="student-pagination">
            <div>
                Menampilkan {{ $siswa->count() }} data siswa pada halaman ini.
            </div>

            <div>
                {{ $siswa->links() }}
            </div>
        </div>

    </section>
</div>

@endsection