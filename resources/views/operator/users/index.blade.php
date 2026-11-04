
@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page-heading', 'Manajemen Pengguna')

@section('content')
<style>
    .users-page {
        color: #333;
        font-family: Arial, sans-serif;
        font-size: 12px;
    }

    .users-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 18px;
    }

    .users-heading h2 {
        margin: 0 0 5px;
        color: #850000;
        font-family: Georgia, serif;
        font-size: 19px;
        font-weight: 700;
    }

    .users-heading p {
        margin: 0;
        color: #888;
        font-size: 11px;
    }

    .users-heading-actions {
        display: flex;
        gap: 7px;
        flex-wrap: wrap;
    }

    .users-btn {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 5px;
        min-height: 33px;
        padding: 0 12px;
        border: 1px solid transparent;
        border-radius: 5px;
        background: #850000;
        color: #fff;
        font-size: 10px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        white-space: nowrap;
    }

    .users-btn:hover {
        background: #690000;
        color: #fff;
    }

    .users-btn.edit {
        min-height: 27px;
        padding: 0 10px;
        background: #f4eeee;
        border-color: #eadada;
        color: #850000;
    }

    .users-btn.edit:hover {
        background: #eadada;
    }

    .users-btn.delete {
        min-height: 27px;
        padding: 0 10px;
        background: #b32626;
        color: #fff;
    }

    .users-btn.delete:hover {
        background: #8f1c1c;
    }

    .users-alert {
        padding: 11px 14px;
        margin-bottom: 13px;
        border: 1px solid;
        border-radius: 6px;
        font-size: 11px;
    }

    .users-alert.success {
        border-color: #cce8d5;
        background: #f0faf3;
        color: #267443;
    }

    .users-alert.error {
        border-color: #f0cccc;
        background: #fff5f5;
        color: #a52b2b;
    }

    .users-alert ul {
        margin: 5px 0 0 17px;
    }

    .users-card {
        overflow: hidden;
        border: 1px solid #e8e5e5;
        border-radius: 8px;
        background: #fff;
    }

    .users-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 13px 15px;
        border-bottom: 1px solid #eee;
    }

    .users-card-header h3 {
        margin: 0;
        color: #850000;
        font-family: Georgia, serif;
        font-size: 13px;
    }

    .users-total {
        color: #888;
        font-size: 10px;
    }

    .users-total strong {
        color: #850000;
        font-size: 13px;
    }

    .users-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .users-table {
        width: 100%;
        min-width: 650px;
        border-collapse: collapse;
        font-size: 10px;
    }

    .users-table th {
        padding: 10px 12px;
        background: #f8eeee;
        color: #666;
        font-size: 9px;
        font-weight: 600;
        text-align: left;
        white-space: nowrap;
    }

    .users-table td {
        padding: 11px 12px;
        border-bottom: 1px solid #eee;
        color: #444;
        vertical-align: middle;
    }

    .users-table tbody tr:hover {
        background: #fcf8f8;
    }

    .users-table tbody tr:last-child td {
        border-bottom: none;
    }

    .users-role {
        display: inline-block;
        padding: 4px 9px;
        border-radius: 20px;
        background: #f5e9e9;
        color: #850000;
        font-size: 9px;
        font-weight: 600;
        white-space: nowrap;
    }

    .users-relation {
        color: #666;
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
    }

    .users-empty {
        padding: 30px 15px !important;
        color: #999 !important;
        text-align: center;
    }

    .users-pagination {
        padding: 13px 15px;
        border-top: 1px solid #eee;
    }

    .users-pagination nav {
        display: flex;
        justify-content: flex-end;
    }

    @media (max-width: 600px) {
        .users-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        .users-card-header {
            align-items: flex-start;
        }
    }
</style>

<div class="users-page">

    <div class="users-heading">
        <div>
            <h2>Manajemen Pengguna</h2>
            <p>Kelola akun operator, guru, dan wali murid.</p>
        </div>

        <div class="users-heading-actions">
            <a href="{{ route('operator.users.create') }}" class="users-btn">
                + Tambah User
            </a>
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
            <h3>Daftar Akun Pengguna</h3>

            <span class="users-total">
                Total akun: <strong>{{ $users->total() }}</strong>
            </span>
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
                        <tr>
                            <td>
                                {{ $users->firstItem() + $loop->index }}
                            </td>

                            <td>
                                <strong>{{ $user->username }}</strong>
                            </td>

                            <td>
                                <span class="users-role">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </td>

                            <td class="users-relation">
                                @if ($user->role === 'wali_murid')
                                    {{ $user->anak?->nama_lengkap ?? 'Belum terhubung' }}
                                @elseif ($user->role === 'guru')
                                    {{ $user->kelasDiampu->pluck('nama_kelas')->join(', ') ?: 'Belum menjadi wali kelas' }}
                                @else
                                    —
                                @endif
                            </td>

                            <td>
                                <div class="users-actions">
                                    <a class="users-btn edit"
                                       href="{{ route('operator.users.edit', $user) }}">
                                        Edit
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
            {{ $users->links() }}
        </div>

    </section>

</div>
@endsection
