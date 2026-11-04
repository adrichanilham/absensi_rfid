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
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->string('nisn', 20)->unique();
            $table->string('nama_lengkap', 150);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->restrictOnDelete();
            $table->string('status', 20)->default('aktif');
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
