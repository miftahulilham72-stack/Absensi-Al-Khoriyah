<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained('peserta')->onDelete('cascade'); // <-- PERBAIKAN: tambahkan 'peserta'
            $table->foreignId('sesi_id')->constrained('sesi')->onDelete('cascade'); // <-- PERBAIKAN: tambahkan 'sesi'
            $table->time('jam_masuk');
            $table->enum('status', ['Tepat Waktu', 'Terlambat']);
            $table->text('ttd_image');
            $table->timestamps();
            
            $table->unique(['peserta_id', 'sesi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};