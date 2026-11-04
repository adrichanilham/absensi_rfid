
@extends('layouts.app')

@section('title', 'Riwayat Absensi Anak')

@section('page-heading', 'Riwayat Absensi Anak')

@section('content')

<style>
    .wali-history {
        color: #292323;
        font-family: Georgia, 'Times New Roman', serif;
        padding: 4px 2px 20px;
    }

    .wali-heading {
        margin-bottom: 18px;
    }

    .wali-heading h2 {
        margin: 0 0 5px;
        color: #850000;
        font-size: 19px;
        font-weight: bold;
    }

    .wali-heading p {
        margin: 0;
        color: #777;
        font-size: 12px;
    }

    .wali-card {
        background: #fff;
        border: 1px solid #e8e1e1;
        border-radius: 8px;
        margin-bottom: 16px;
        overflow: hidden;
    }

    .wali-card-header {
        padding: 12px 15px;
        background: #850000;
        color: #fff;
    }

    .wali-card-header h3 {
        margin: 0;
        font-size: 15px;
        font-weight: bold;
    }

    .wali-card-header p {
        margin: 4px 0 0;
        color: #f1dada;
        font-family: Arial, sans-serif;
        font-size: 11px;
    }

    .wali-card-body {
        padding: 15px;
    }

    .wali-filter {
        display: flex;
        align-items: end;
        flex-wrap: wrap;
        gap: 10px;
    }

    .wali-field {
        display: flex;
        flex-direction: column;
        gap: 5px;
        min-width: 165px;
    }

    .wali-field label {
        color: #514747;
        font-family: Arial, sans-serif;
        font-size: 11px;
        font-weight: bold;
    }

    .wali-field input {
        width: 100%;
        height: 34px;
        padding: 7px 9px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background: #fff;
        color: #333;
        font-family: Arial, sans-serif;
        font-size: 11px;
        outline: none;
    }

    .wali-field input:focus {
        border-color: #850000;
        box-shadow: 0 0 0 2px #85000015;
    }

    .wali-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 34px;
        padding: 7px 12px;
        border: 0;
        border-radius: 5px;
        background: #850000;
        color: #fff;
        font-family: Arial, sans-serif;
        font-size: 11px;
        font-weight: bold;
        text-decoration: none;
        cursor: pointer;
        transition: background .2s;
    }

    .wali-button:hover {
        background: #650000;
        color: #fff;
    }

    .wali-button-reset {
        background: #eee8e8;
        color: #650000;
    }

    .wali-button-reset:hover {
        background: #e3d5d5;
        color: #650000;
    }

    .wali-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .wali-table {
        width: 100%;
        min-width: 650px;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
    }

    .wali-table th {
        padding: 10px 12px;
        background: #f5eeee;
        color: #700000;
        font-size: 11px;
        font-weight: bold;
        text-align: left;
        white-space: nowrap;
    }

    .wali-table td {
        padding: 10px 12px;
        border-bottom: 1px solid #eee8e8;
        color: #484040;
        font-size: 11px;
        vertical-align: middle;
    }

    .wali-table tbody tr:hover {
        background: #fffafa;
    }

    .wali-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .wali-status {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        background: #eee;
        color: #555;
        font-size: 10px;
        font-weight: bold;
        text-transform: capitalize;
        white-space: nowrap;
    }

    .wali-status.status-hadir {
        background: #e2f4e7;
        color: #18733a;
    }

    .wali-status.status-terlambat {
        background: #fff0dc;
        color: #a85a00;
    }

    .wali-status.status-izin {
        background: #e7efff;
        color: #2856a3;
    }

    .wali-status.status-sakit {
        background: #e0f5f6;
        color: #087780;
    }

    .wali-status.status-alpa {
        background: #fde8e8;
        color: #a51f1f;
    }

    .wali-empty {
        padding: 25px 12px !important;
        color: #888 !important;
        text-align: center;
    }

    .wali-error {
        display: block;
        margin-top: 3px;
        color: #b42318;
        font-family: Arial, sans-serif;
        font-size: 10px;
    }

    .wali-pagination {
        padding: 12px 14px;
        border-top: 1px solid #eee;
        font-family: Arial, sans-serif;
        font-size: 12px;
    }

    @media (max-width: 600px) {
        .wali-card-body {
            padding: 12px;
        }

        .wali-filter {
            align-items: stretch;
        }

        .wali-field {
            width: 100%;
        }

        .wali-button {
            flex: 1;
        }

        .wali-heading h2 {
            font-size: 17px;
        }
    }
</style>

<div class="wali-history">

    <div class="wali-heading">
        <h2>Riwayat Absensi Anak</h2>
        <p>
            {{ $anak->nama_lengkap }} —
            {{ $anak->kelas->nama_kelas ?? '-' }}
        </p>
    </div>

    <section class="wali-card">

        <div class="wali-card-header">
            <h3>Filter Tanggal</h3>
            <p>Pilih periode untuk melihat riwayat kehadiran anak</p>
        </div>

        <div class="wali-card-body">
            <form action="{{ route('wali.absensi.index') }}"
                  method="GET"
                  class="wali-filter">

                <div class="wali-field">
                    <label for="tanggal_mulai">Dari tanggal</label>
                    <input
                        type="date"
                        id="tanggal_mulai"
                        name="tanggal_mulai"
                        value="{{ request('tanggal_mulai') }}"
                    >

                    @error('tanggal_mulai')
                        <span class="wali-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="wali-field">
                    <label for="tanggal_selesai">Sampai tanggal</label>
                    <input
                        type="date"
                        id="tanggal_selesai"
                        name="tanggal_selesai"
                        value="{{ request('tanggal_selesai') }}"
                    >

                    @error('tanggal_selesai')
                        <span class="wali-error">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="wali-button">
                    Terapkan Filter
                </button>

                <a class="wali-button wali-button-reset"
                   href="{{ route('wali.absensi.index') }}">
                    Reset
                </a>

            </form>
        </div>
    </section>

    <section class="wali-card">

        <div class="wali-card-header">
            <h3>Daftar Kehadiran</h3>
            <p>Catatan kehadiran siswa berdasarkan periode yang dipilih</p>
        </div>

        <div class="wali-table-wrap">
            <table class="wali-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Tanggal</th>
                        <th>Waktu Masuk</th>
                        <th>Waktu Keluar</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($absensi as $item)
                        <tr>
                            <td>{{ $absensi->firstItem() + $loop->index }}</td>

                            <td>
                                {{ $item->tanggal->format('d-m-Y') }}
                            </td>

                            <td>{{ $item->waktu_masuk ?? '-' }}</td>

                            <td>{{ $item->waktu_keluar ?? '-' }}</td>

                            <td>
                                <span class="wali-status status-{{ $item->status }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>

                            <td>{{ $item->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="wali-empty">
                                Belum ada data absensi untuk periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="wali-pagination">
            {{ $absensi->links() }}
        </div>

    </section>

</div>

@endsection
