<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengajuan_izin', function (Blueprint $table) {
            $table->id();

            $table->foreignId('siswa_id')
                ->constrained('siswa')
                ->restrictOnDelete();

            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');

            $table->enum('jenis', ['izin', 'sakit']);
            $table->text('alasan');
            $table->string('dokumen_path')->nullable();

            $table->enum('status', [
                'menunggu',
                'disetujui',
                'ditolak',
            ])->default('menunggu');

            $table->foreignId('diverifikasi_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('catatan_verifikasi')->nullable();
            $table->timestamp('diverifikasi_pada')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_izin');
    }
};
