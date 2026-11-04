<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\PengajuanIzin;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OperatorPengajuanIzinController extends Controller
{
    /**
     * Menampilkan daftar pengajuan izin/sakit.
     */
    public function index(Request $request)
    {
        $request->validate([
            'status' => [
                'nullable',
                'in:menunggu,disetujui,ditolak',
            ],
        ]);

        $query = PengajuanIzin::with(['siswa.kelas', 'verifier'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pengajuan = $query->paginate(10)->withQueryString();

        return view('operator.pengajuan.index', compact('pengajuan'));
    }

    /**
     * Menyetujui atau menolak pengajuan.
     */
    public function verifikasi(Request $request, PengajuanIzin $pengajuan)
    {
        $data = $request->validate([
            'status' => [
                'required',
                'in:disetujui,ditolak',
            ],
            'catatan_verifikasi' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $hasil = DB::transaction(function () use ($data, $pengajuan) {
            // Kunci data pengajuan agar tidak diproses bersamaan.
            $pengajuan = PengajuanIzin::whereKey($pengajuan->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($pengajuan->status !== 'menunggu') {
                return [
                    'berhasil' => false,
                    'pesan' => 'Pengajuan ini sudah diverifikasi sebelumnya.',
                ];
            }

            // Catatan absensi hanya dibuat jika pengajuan disetujui.
            if ($data['status'] === 'disetujui') {
                $tanggalKonflik = [];

                $periode = CarbonPeriod::create(
                    $pengajuan->tanggal_mulai,
                    $pengajuan->tanggal_selesai
                );

                foreach ($periode as $tanggal) {
                    $tanggalString = $tanggal->toDateString();

                    // Jangan menimpa absensi yang sudah tercatat.
                    $absensiAda = Absensi::where(
                        'siswa_id',
                        $pengajuan->siswa_id
                    )
                        ->whereDate('tanggal', $tanggalString)
                        ->lockForUpdate()
                        ->exists();

                    if ($absensiAda) {
                        $tanggalKonflik[] = $tanggal->format('d-m-Y');
                        continue;
                    }

                    // Buat catatan izin/sakit tanpa waktu scan RFID.
                    Absensi::create([
                        'siswa_id' => $pengajuan->siswa_id,
                        'tanggal' => $tanggalString,
                        'waktu_masuk' => null,
                        'waktu_keluar' => null,
                        'status' => $pengajuan->jenis,
                        'keterangan' => 'Pengajuan '
                            . $pengajuan->jenis
                            . ' disetujui (pengajuan #'
                            . $pengajuan->id
                            . ').',
                    ]);
                }
            } else {
                $tanggalKonflik = [];
            }

            // Simpan keputusan Operator.
            $pengajuan->update([
                'status' => $data['status'],
                'catatan_verifikasi' =>
                    $data['catatan_verifikasi'] ?? null,
                'diverifikasi_oleh' => auth()->id(),
                'diverifikasi_pada' => now(),
            ]);

            return [
                'berhasil' => true,
                'status' => $data['status'],
                'konflik' => $tanggalKonflik,
            ];
        });

        if (! $hasil['berhasil']) {
            return back()->with('error', $hasil['pesan']);
        }

        if ($hasil['status'] === 'ditolak') {
            return redirect()
                ->route('operator.pengajuan.index')
                ->with('success', 'Pengajuan ditolak. Data absensi tidak diubah.');
        }

        $pesan = 'Pengajuan disetujui dan catatan absensi untuk tanggal yang kosong berhasil dibuat.';

        if (! empty($hasil['konflik'])) {
            $pesan .= ' Tanggal berikut sudah memiliki catatan absensi dan tidak diubah: '
                . implode(', ', $hasil['konflik'])
                . '. Periksa tanggal tersebut secara manual.';
        }

        return redirect()
            ->route('operator.pengajuan.index')
            ->with('success', $pesan);
    }

    /**
     * Mengunduh dokumen pendukung dari penyimpanan privat.
     */
    public function downloadDokumen(PengajuanIzin $pengajuan)
    {
        if (
            ! $pengajuan->dokumen_path ||
            ! Storage::disk('local')->exists($pengajuan->dokumen_path)
        ) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        return Storage::disk('local')->download(
            $pengajuan->dokumen_path
        );
    }
}
