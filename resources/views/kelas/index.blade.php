
@extends('layouts.app')

@section('title', 'Data Kelas - Absensi RFID')

@section('page-heading', 'Data Kelas')

@section('content')

<style>
    .class-page {
        width: 100%;
        font-family: Arial, sans-serif;
        color: #292929;
    }

    .class-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 20px;
    }

    .class-heading h2 {
        margin: 0 0 5px;
        color: #850000;
        font-family: Georgia, serif;
        font-size: 20px;
    }

    .class-heading p {
        margin: 0;
        color: #777;
        font-family: Georgia, serif;
        font-size: 12px;
        font-style: italic;
    }

    .class-date {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 13px;
        background: #850000;
        color: white;
        border-radius: 7px;
        font-family: Georgia, serif;
        font-size: 12px;
        font-weight: bold;
    }

    .class-table-panel {
        background: white;
        border: 1px solid #e5dddd;
        border-radius: 8px;
        overflow: hidden;
    }

    .class-table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding: 13px 15px;
        background: #850000;
        color: white;
    }

    .class-table-header h3 {
        margin: 0 0 4px;
        font-family: Georgia, serif;
        font-size: 16px;
    }

    .class-table-header p {
        margin: 0;
        color: #f1dada;
        font-size: 10px;
    }

    .class-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px 12px;
        border: 1px solid transparent;
        border-radius: 6px;
        text-decoration: none;
        font-family: Arial, sans-serif;
        font-size: 11px;
        font-weight: bold;
        cursor: pointer;
        white-space: nowrap;
        transition: .2s;
    }

    .class-btn-add {
        background: white;
        color: #850000;
    }

    .class-btn-add:hover {
        background: #fff0f0;
    }

    .class-btn-edit {
        background: white;
        color: #850000;
        border-color: #e6caca;
    }

    .class-btn-edit:hover {
        background: #850000;
        color: white;
        border-color: #850000;
    }

    .class-btn-delete {
        background: white;
        color: #b91c1c;
        border-color: #f1caca;
    }

    .class-btn-delete:hover {
        background: #b91c1c;
        color: white;
        border-color: #b91c1c;
    }

    .class-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .class-table {
        width: 100%;
        min-width: 600px;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
    }

    .class-table thead th {
        padding: 11px 12px;
        background: #f3eeee;
        color: #700000;
        font-size: 11px;
        font-weight: bold;
        text-align: left;
        white-space: nowrap;
        border-bottom: 1px solid #e6dede;
    }

    .class-table tbody td {
        padding: 12px;
        border-bottom: 1px solid #eee;
        color: #444;
        font-size: 11px;
        vertical-align: middle;
    }

    .class-table tbody tr:hover {
        background: #fff8f8;
    }

    .class-table tbody tr:last-child td {
        border-bottom: none;
    }

    .class-name {
        color: #292929;
        font-weight: bold;
    }

    .class-count {
        display: inline-block;
        min-width: 30px;
        padding: 5px 9px;
        border-radius: 15px;
        background: #f3eeee;
        color: #850000;
        text-align: center;
        font-size: 10px;
        font-weight: bold;
    }

    .class-wali {
        color: #555;
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
        padding: 5px 8px;
        border-radius: 5px;
        background: #fff3cd;
        color: #946200;
        font-size: 10px;
        white-space: nowrap;
    }

    .class-empty {
        padding: 35px 15px !important;
        text-align: center;
        color: #999 !important;
        font-size: 12px !important;
    }

    .class-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        padding: 12px 15px;
        border-top: 1px solid #eee;
        font-size: 11px;
        color: #777;
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
        min-width: 28px;
        height: 28px;
        padding: 0 8px;
        border: 1px solid #e5dddd;
        border-radius: 5px;
        color: #850000;
        background: white;
        text-decoration: none;
        font-size: 10px;
    }

    .class-pagination nav span[aria-current="page"] span {
        background: #850000;
        border-color: #850000;
        color: white;
    }

    .class-pagination nav a:hover {
        background: #fff1f1;
    }

    @media (max-width: 600px) {
        .class-heading h2 {
            font-size: 18px;
        }

        .class-date {
            font-size: 10px;
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
            <span>🗓️</span>
            {{ now()->translatedFormat('l, d F Y') }}
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
                                <span class="class-wali">
                                    {{ $item->waliKelas?->username ?? 'Belum ditentukan' }}
                                </span>
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
