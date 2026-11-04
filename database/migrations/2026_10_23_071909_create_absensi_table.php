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
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('siswa_id')
                ->constrained('siswa')
                ->restrictOnDelete();

            $table->date('tanggal');
            $table->time('waktu_masuk')->nullable();
            $table->time('waktu_keluar')->nullable();

            $table->enum('status', [
                'hadir',
                'terlambat',
                'sakit',
                'izin',
                'alpa',
            ])->default('hadir');

            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(
                ['siswa_id', 'tanggal'],
                'absensi_siswa_tanggal_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
