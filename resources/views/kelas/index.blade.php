@extends('layouts.app')

@section('title', 'Data Kelas - Absensi RFID')

@section('page-heading', 'Data Kelas')

@section('content')

@php
    $tanggalIndonesia = now()->locale('id')->translatedFormat('l, d F Y');
@endphp

<style>
    .class-page {
        width: 100%;
        font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, sans-serif;
        color: #1f2937;
    }

    .class-heading {
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

    .class-heading h2 {
        margin: 0 0 4px;
        color: #850000;
        font-family: inherit;
        font-size: 19px;
        font-weight: 700;
        letter-spacing: -0.015em;
    }

    .class-heading p {
        margin: 0;
        color: #6b7280;
        font-family: inherit;
        font-size: 13px;
        font-weight: 500;
    }

    .class-date {
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

    .class-date svg {
        width: 17px;
        height: 17px;
        flex-shrink: 0;
    }

    .class-table-panel {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }

    .class-table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding: 15px 20px;
        background: #850000;
        color: #ffffff;
    }

    .class-table-header h3 {
        margin: 0 0 3px;
        font-family: inherit;
        font-size: 15.5px;
        font-weight: 700;
        color: #ffffff;
    }

    .class-table-header p {
        margin: 0;
        color: #f3d1d1;
        font-size: 12px;
    }

    /* TOMBOL */
    .class-btn {
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

    .class-btn-add {
        background: #ffffff;
        color: #850000;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .class-btn-add:hover {
        background: #fdf2f2;
    }

    .class-btn-edit {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        background: #ffffff;
        color: #850000;
        border-color: #e6caca;
    }

    .class-btn-edit:hover {
        background: #850000;
        color: #ffffff;
        border-color: #850000;
    }

    .class-btn-delete {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        background: #ffffff;
        color: #b91c1c;
        border-color: #fecaca;
    }

    .class-btn-delete:hover {
        background: #b91c1c;
        color: #ffffff;
        border-color: #b91c1c;
    }

    /* TABEL */
    .class-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .class-table {
        width: 100%;
        min-width: 600px;
        border-collapse: collapse;
        font-family: inherit;
    }

    .class-table thead th {
        padding: 12px 14px;
        background: #f9fafb;
        color: #374151;
        font-size: 12px;
        font-weight: 700;
        text-align: left;
        white-space: nowrap;
        border-bottom: 1px solid #e5e7eb;
    }

    .class-table tbody td {
        padding: 12px 14px;
        border-bottom: 1px solid #f3f4f6;
        color: #374151;
        font-size: 13px;
        vertical-align: middle;
    }

    .class-table tbody tr:hover {
        background: #fdf8f8;
    }

    .class-table tbody tr:last-child td {
        border-bottom: none;
    }

    .class-name {
        color: #111827;
        font-weight: 600;
    }

    .class-count {
        display: inline-block;
        min-width: 34px;
        padding: 4px 10px;
        border-radius: 6px;
        background: #fdf2f2;
        border: 1px solid #f5c6cb;
        color: #850000;
        text-align: center;
        font-size: 12px;
        font-weight: 700;
    }

    .class-wali {
        color: #4b5563;
    }

    .class-wali-empty {
        color: #9ca3af;
        font-style: italic;
        font-size: 12px;
    }

    .class-actions {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .class-actions form {
        display: inline-flex;
        margin: 0;
    }

    .class-note {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 6px;
        background: #fef3c7;
        color: #b45309;
        font-size: 11.5px;
        font-weight: 600;
        white-space: nowrap;
    }

    .class-empty {
        padding: 32px 16px !important;
        text-align: center;
        color: #6b7280 !important;
        font-size: 13px !important;
    }

    /* PAGINATION */
    .class-pagination {
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

    .class-pagination nav {
        display: flex;
        justify-content: flex-end;
    }

    .class-pagination nav > div:first-child {
        display: none;
    }

    .class-pagination nav > div:last-child {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .class-pagination nav a,
    .class-pagination nav span[aria-current="page"] span,
    .class-pagination nav span[aria-disabled="true"] span {
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

    .class-pagination nav span[aria-current="page"] span {
        background: #850000;
        border-color: #850000;
        color: #ffffff;
    }

    .class-pagination nav a:hover {
        background: #fdf2f2;
        border-color: #850000;
        color: #850000;
    }

    @media (max-width: 600px) {
        .class-heading {
            padding: 16px;
            flex-direction: column;
            align-items: flex-start;
        }

        .class-heading h2 {
            font-size: 17px;
        }

        .class-date {
            font-size: 12px;
            padding: 7px 12px;
        }

        .class-table-header {
            align-items: flex-start;
        }

        .class-pagination {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="class-page">

    <div class="class-heading">
        <div>
            <h2>Data Kelas</h2>
            <p>Pengelolaan kelas SDN 37 Sungai Bangek.</p>
        </div>

        <div class="class-date">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            <span>{{ $tanggalIndonesia }}</span>
        </div>
    </div>

    <section class="class-table-panel">

        <div class="class-table-header">
            <div>
                <h3>Daftar Kelas</h3>
                <p>Informasi kelas, wali kelas, dan jumlah siswa</p>
            </div>

            @if(auth()->user()->role !== 'guru')
                <a href="{{ route('kelas.create') }}"
                   class="class-btn class-btn-add">
                    + Tambah Kelas
                </a>
            @endif
        </div>

        <div class="class-table-wrapper">
            <table class="class-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Kelas</th>
                        <th>Wali Kelas</th>
                        <th>Jumlah Siswa</th>

                        @if(auth()->user()->role !== 'guru')
                            <th>Aksi</th>
                        @endif
                    </tr>
                </thead>

                <tbody>
                    @forelse ($kelas as $item)
                        <tr>
                            <td>
                                {{ $kelas->firstItem() + $loop->index }}
                            </td>

                            <td>
                                <span class="class-name">
                                    {{ $item->nama_kelas }}
                                </span>
                            </td>

                            <td>
                                @if ($item->waliKelas?->username)
                                    <span class="class-wali">
                                        {{ $item->waliKelas->username }}
                                    </span>
                                @else
                                    <span class="class-wali-empty">Belum ditentukan</span>
                                @endif
                            </td>

                            <td>
                                <span class="class-count">
                                    {{ $item->siswa_count }}
                                </span>
                            </td>

                            @if(auth()->user()->role !== 'guru')
                                <td>
                                    <div class="class-actions">
                                        <a href="{{ route('kelas.edit', $item) }}"
                                           class="class-btn class-btn-edit">
                                            ✎ Edit
                                        </a>

                                        @if ($item->siswa_count === 0)
                                            <form
                                                action="{{ route('kelas.destroy', $item) }}"
                                                method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus kelas ini?')"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="class-btn class-btn-delete">
                                                    Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span class="class-note">
                                                Masih digunakan
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role !== 'guru' ? 5 : 4 }}"
                                class="class-empty">
                                Belum ada data kelas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="class-pagination">
            <div>
                Menampilkan {{ $kelas->count() }} data kelas pada halaman ini.
            </div>

            <div>
                {{ $kelas->links() }}
            </div>
        </div>

    </section>
</div>

@endsection