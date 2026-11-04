
@extends('layouts.app')

@section('title', 'Pengajuan Izin/Sakit')

@section('page-heading', 'Pengajuan Izin/Sakit')

@section('content')

<style>
    .wali-submission {
        font-family: Georgia, 'Times New Roman', serif;
        color: #292323;
        padding: 4px 2px 20px;
    }

    .submission-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 18px;
    }

    .submission-heading h2 {
        margin: 0 0 5px;
        color: #850000;
        font-size: 19px;
        font-weight: bold;
    }

    .submission-heading p {
        margin: 0;
        color: #777;
        font-size: 12px;
    }

    .submission-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 9px 13px;
        border: none;
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

    .submission-button:hover {
        background: #650000;
        color: white;
    }

    .submission-alert {
        padding: 10px 13px;
        margin-bottom: 14px;
        border: 1px solid #b8dfc2;
        border-radius: 6px;
        background: #edf8f0;
        color: #236b37;
        font-family: Arial, sans-serif;
        font-size: 12px;
    }

    .submission-card {
        margin-bottom: 16px;
        overflow: hidden;
        border: 1px solid #e8e1e1;
        border-radius: 8px;
        background: #fff;
    }

    .submission-card-header {
        padding: 12px 15px;
        background: #850000;
        color: white;
    }

    .submission-card-header h3 {
        margin: 0;
        font-size: 15px;
        font-weight: bold;
    }

    .submission-card-header p {
        margin: 4px 0 0;
        color: #f1dada;
        font-family: Arial, sans-serif;
        font-size: 11px;
    }

    .submission-student-info {
        display: flex;
        flex-wrap: wrap;
        gap: 10px 35px;
        padding: 13px 15px;
    }

    .submission-student-info p {
        margin: 0;
        color: #514747;
        font-family: Arial, sans-serif;
        font-size: 12px;
    }

    .submission-student-info strong {
        color: #700000;
    }

    .submission-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .submission-table {
        width: 100%;
        min-width: 750px;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
    }

    .submission-table th {
        padding: 10px 12px;
        background: #f5eeee;
        color: #700000;
        font-size: 11px;
        font-weight: bold;
        text-align: left;
        white-space: nowrap;
    }

    .submission-table td {
        padding: 10px 12px;
        border-bottom: 1px solid #eee8e8;
        color: #484040;
        font-size: 11px;
        line-height: 1.5;
        vertical-align: top;
    }

    .submission-table tbody tr:hover {
        background: #fffafa;
    }

    .submission-table tbody tr:last-child td {
        border-bottom: none;
    }

    .submission-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: bold;
        white-space: nowrap;
    }

    .submission-badge.menunggu {
        background: #fff2d8;
        color: #986000;
    }

    .submission-badge.disetujui {
        background: #e2f4e7;
        color: #18733a;
    }

    .submission-badge.ditolak {
        background: #fde8e8;
        color: #a51f1f;
    }

    .submission-empty {
        padding: 28px 12px !important;
        color: #888 !important;
        text-align: center;
    }

    .submission-empty h3 {
        margin: 0 0 5px;
        color: #850000;
        font-size: 15px;
    }

    .submission-empty p {
        margin: 0;
        color: #888;
        font-size: 11px;
    }

    .submission-pagination {
        padding: 12px 14px;
        border-top: 1px solid #eee;
        font-family: Arial, sans-serif;
        font-size: 12px;
    }

    @media (max-width: 600px) {
        .submission-heading {
            align-items: flex-start;
        }

        .submission-heading h2 {
            font-size: 17px;
        }

        .submission-student-info {
            flex-direction: column;
            gap: 8px;
        }

        .submission-button {
            width: 100%;
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
                {{ $anak->NISN }}
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
                                    {{ ucfirst($item->jenis) }}
                                </td>

                                <td>
                                    {{ $item->tanggal_mulai->format('d-m-Y') }}
                                    <br>
                                    sampai
                                    <br>
                                    {{ $item->tanggal_selesai->format('d-m-Y') }}
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
                {{ $pengajuan->links() }}
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
