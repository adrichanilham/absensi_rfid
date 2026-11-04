
@extends('layouts.app')

@section('title', 'Data Siswa - Absensi RFID')

@section('page-heading', 'Data Siswa')

@section('content')

<style>
    .student-page {
        width: 100%;
        font-family: Arial, sans-serif;
        color: #292929;
    }

    .student-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 20px;
    }

    .student-heading h2 {
        margin: 0 0 5px;
        color: #850000;
        font-family: Georgia, serif;
        font-size: 20px;
    }

    .student-heading p {
        margin: 0;
        color: #777;
        font-family: Georgia, serif;
        font-size: 12px;
        font-style: italic;
    }

    .student-date {
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

    .student-alert {
        padding: 11px 14px;
        margin-bottom: 15px;
        border-radius: 7px;
        font-size: 13px;
    }

    .student-alert.success {
        background: #dcfce7;
        color: #166534;
    }

    .student-alert.error {
        background: #fee2e2;
        color: #991b1b;
    }

    .student-table-panel {
        background: white;
        border: 1px solid #e5dddd;
        border-radius: 8px;
        overflow: hidden;
    }

    .student-table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding: 13px 15px;
        background: #850000;
        color: white;
    }

    .student-table-header h3 {
        margin: 0 0 4px;
        font-family: Georgia, serif;
        font-size: 16px;
    }

    .student-table-header p {
        margin: 0;
        color: #f1dada;
        font-size: 10px;
    }

    .student-count {
        padding: 6px 9px;
        border-radius: 5px;
        background: rgba(255,255,255,.15);
        color: white;
        font-size: 10px;
    }

    .student-btn {
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
        transition: .2s;
        white-space: nowrap;
    }

    .student-btn-add {
        background: white;
        color: #850000;
    }

    .student-btn-add:hover {
        background: #fff0f0;
    }

    .student-btn-edit {
        background: white;
        color: #850000;
        border-color: #e6caca;
    }

    .student-btn-edit:hover {
        background: #850000;
        color: white;
        border-color: #850000;
    }

    .student-btn-delete {
        background: white;
        color: #b91c1c;
        border-color: #f1caca;
    }

    .student-btn-delete:hover {
        background: #b91c1c;
        color: white;
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
        font-family: Arial, sans-serif;
    }

    .student-table thead th {
        padding: 11px 10px;
        background: #f3eeee;
        color: #700000;
        font-size: 11px;
        font-weight: bold;
        text-align: left;
        white-space: nowrap;
        border-bottom: 1px solid #e6dede;
    }

    .student-table tbody td {
        padding: 10px;
        border-bottom: 1px solid #eee;
        color: #444;
        font-size: 11px;
        vertical-align: middle;
    }

    .student-table tbody tr:hover {
        background: #fff8f8;
    }

    .student-table tbody tr:last-child td {
        border-bottom: none;
    }

    .student-name {
        color: #292929;
        font-weight: bold;
    }

    .student-nisn {
        display: block;
        margin-top: 3px;
        color: #999;
        font-size: 9px;
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
        min-width: 55px;
        padding: 5px 9px;
        border-radius: 20px;
        text-align: center;
        font-size: 10px;
        font-weight: bold;
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
        padding: 35px 15px !important;
        text-align: center;
        color: #999 !important;
        font-size: 12px !important;
    }

    .student-pagination {
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

    .student-pagination nav span[aria-current="page"] span {
        background: #850000;
        border-color: #850000;
        color: white;
    }

    .student-pagination nav a:hover {
        background: #fff1f1;
    }

    @media (max-width: 600px) {
        .student-heading h2 {
            font-size: 18px;
        }

        .student-date {
            font-size: 10px;
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
            <span>🗓️</span>
            {{ now()->translatedFormat('l, d F Y') }}
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
                                {{ $item->rfid?->uid ?? 'Belum terdaftar' }}
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
