<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peserta', function (Blueprint $table) {
            $table->id();
            $table->string('nis', 20)->unique();
            $table->string('nama_lengkap', 100);
            $table->enum('lembaga', ['MTs', 'MA']);
            $table->string('gugus', 50)->nullable();
            $table->timestamps();
            
            $table->index('nis');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta');
    }
};