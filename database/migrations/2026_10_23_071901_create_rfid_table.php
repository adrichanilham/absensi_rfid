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
        Schema::create('rfid', function (Blueprint $table) {
            $table->id();
            $table->string('uid', 100)->unique();
    
            $table->foreignId('siswa_id')
                ->constrained('siswa')
                ->restrictOnDelete();
    
            $table->boolean('aktif')->default(true);
            $table->timestamp('terdaftar_pada')->nullable();
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('rfid');
    }
};
