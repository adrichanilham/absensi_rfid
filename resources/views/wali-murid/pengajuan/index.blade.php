@extends('layouts.app')

@section('title', 'Pengajuan Izin/Sakit - Absensi RFID')

@section('page-heading', 'Pengajuan Izin/Sakit')

@section('content')

<style>
    .wali-submission {
        font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, sans-serif;
        color: #1f2937;
        width: 100%;
    }

    /* HEADER HALAMAN */
    .submission-heading {
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

    .submission-heading h2 {
        margin: 0 0 4px;
        color: #850000;
        font-family: inherit;
        font-size: 19px;
        font-weight: 700;
        letter-spacing: -0.015em;
    }

    .submission-heading p {
        margin: 0;
        color: #6b7280;
        font-family: inherit;
        font-size: 13px;
        font-weight: 500;
    }

    .submission-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 9px 16px;
        border: none;
        border-radius: 8px;
        background: #850000;
        color: #ffffff;
        font-family: inherit;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        white-space: nowrap;
        transition: background 0.18s ease;
    }

    .submission-button:hover {
        background: #680000;
        color: #ffffff;
    }

    /* NOTIFIKASI */
    .submission-alert {
        padding: 12px 15px;
        margin-bottom: 16px;
        border: 1px solid #bbf7d0;
        border-radius: 8px;
        background: #f0fdf4;
        color: #166534;
        font-family: inherit;
        font-size: 13px;
        font-weight: 500;
    }

    /* KARTU */
    .submission-card {
        margin-bottom: 20px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }

    .submission-card-header {
        padding: 15px 20px;
        background: #850000;
        color: #ffffff;
    }

    .submission-card-header h3 {
        margin: 0 0 3px;
        font-family: inherit;
        font-size: 15.5px;
        font-weight: 700;
        color: #ffffff;
    }

    .submission-card-header p {
        margin: 0;
        color: #f3d1d1;
        font-family: inherit;
        font-size: 12px;
    }

    .submission-student-info {
        display: flex;
        flex-wrap: wrap;
        gap: 12px 40px;
        padding: 16px 20px;
    }

    .submission-student-info p {
        margin: 0;
        color: #374151;
        font-family: inherit;
        font-size: 13px;
    }

    .submission-student-info strong {
        color: #850000;
        font-weight: 700;
    }

    /* TABEL */
    .submission-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .submission-table {
        width: 100%;
        min-width: 750px;
        border-collapse: collapse;
        font-family: inherit;
    }

    .submission-table th {
        padding: 12px 14px;
        background: #f9fafb;
        color: #374151;
        font-size: 12px;
        font-weight: 700;
        text-align: left;
        white-space: nowrap;
        border-bottom: 1px solid #e5e7eb;
    }

    .submission-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #f3f4f6;
        color: #374151;
        font-size: 13px;
        line-height: 1.5;
        vertical-align: top;
    }

    .submission-table tbody tr:hover {
        background: #fdf8f8;
    }

    .submission-table tbody tr:last-child td {
        border-bottom: none;
    }

    .submission-date-range {
        font-size: 12.5px;
        color: #374151;
        white-space: nowrap;
    }

    .submission-date-sep {
        display: block;
        font-size: 11px;
        color: #6b7280;
    }

    /* BADGE STATUS */
    .submission-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11.5px;
        font-weight: 700;
        white-space: nowrap;
    }

    .submission-badge.menunggu {
        background: #fef3c7;
        color: #b45309;
    }

    .submission-badge.disetujui {
        background: #dcfce7;
        color: #15803d;
    }

    .submission-badge.ditolak {
        background: #fee2e2;
        color: #b91c1c;
    }

    /* DATA KOSONG */
    .submission-empty {
        padding: 36px 16px;
        color: #6b7280;
        text-align: center;
    }

    .submission-empty h3 {
        margin: 0 0 6px;
        color: #850000;
        font-family: inherit;
        font-size: 15.5px;
        font-weight: 700;
    }

    .submission-empty p {
        margin: 0;
        color: #6b7280;
        font-size: 13px;
    }

    /* PAGINATION */
    .submission-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        padding: 13px 18px;
        border-top: 1px solid #e5e7eb;
        background: #fafafa;
        font-family: inherit;
        font-size: 12px;
        color: #6b7280;
    }

    .submission-pagination nav {
        display: flex;
        justify-content: flex-end;
    }

    .submission-pagination nav > div:first-child {
        display: none;
    }

    .submission-pagination nav > div:last-child {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .submission-pagination nav a,
    .submission-pagination nav span[aria-current="page"] span,
    .submission-pagination nav span[aria-disabled="true"] span {
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

    .submission-pagination nav span[aria-current="page"] span {
        background: #850000;
        border-color: #850000;
        color: #ffffff;
    }

    .submission-pagination nav a:hover {
        background: #fdf2f2;
        border-color: #850000;
        color: #850000;
    }

    @media (max-width: 600px) {
        .submission-heading {
            padding: 16px;
            flex-direction: column;
            align-items: flex-start;
        }

        .submission-heading h2 {
            font-size: 17px;
        }

        .submission-student-info {
            flex-direction: column;
            gap: 8px;
            padding: 14px 16px;
        }

        .submission-button {
            width: 100%;
        }

        .submission-pagination {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="wali-submission">

    <div class="submission-heading">
        <div>
            <h2>Daftar Pengajuan Izin/Sakit</h2>
            <p>Kelola dan pantau pengajuan izin atau sakit anak Anda.</p>
        </div>

        <a href="{{ route('wali.pengajuan.create') }}"
           class="submission-button">
            + Buat Pengajuan
        </a>
    </div>

    @if (session('success'))
        <div class="submission-alert">
            {{ session('success') }}
        </div>
    @endif

    <section class="submission-card">

        <div class="submission-card-header">
            <h3>Informasi Siswa</h3>
            <p>Data anak yang terhubung dengan akun wali murid</p>
        </div>

        <div class="submission-student-info">
            <p>
                <strong>Nama Siswa:</strong>
                {{ $anak->nama_lengkap }}
            </p>

            <p>
                <strong>NISN:</strong>
                {{ $anak->nisn ?? $anak->NISN }}
            </p>
        </div>

    </section>

    <section class="submission-card">

        <div class="submission-card-header">
            <h3>Riwayat Pengajuan</h3>
            <p>Daftar pengajuan izin dan sakit yang telah dikirim</p>
        </div>

        @if ($pengajuan->count())
            <div class="submission-table-wrap">
                <table class="submission-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Jenis</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Alasan</th>
                            <th>Status</th>
                            <th>Catatan Operator</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($pengajuan as $item)
                            <tr>
                                <td>
                                    {{ $pengajuan->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    <strong>{{ ucfirst($item->jenis) }}</strong>
                                </td>

                                <td>
                                    <div class="submission-date-range">
                                        {{ $item->tanggal_mulai->format('d-m-Y') }}
                                        <span class="submission-date-sep">s/d</span>
                                        {{ $item->tanggal_selesai->format('d-m-Y') }}
                                    </div>
                                </td>

                                <td>
                                    {{ $item->alasan }}
                                </td>

                                <td>
                                    <span class="submission-badge {{ $item->status }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>

                                <td>
                                    {{ $item->catatan_verifikasi ?: '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="submission-pagination">
                <div>
                    Menampilkan {{ $pengajuan->count() }} riwayat pengajuan pada halaman ini.
                </div>

                <div>
                    {{ $pengajuan->links() }}
                </div>
            </div>

        @else
            <div class="submission-empty">
                <h3>Belum Ada Pengajuan</h3>
                <p>Pengajuan izin atau sakit yang Anda kirim akan muncul di sini.</p>
            </div>
        @endif

    </section>

</div>

@endsection