<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sesi', 100);
            $table->time('batas_waktu');
            $table->boolean('is_active')->default(false);
            $table->enum('peruntukan', ['MTs', 'MA', 'Semua'])->default('Semua');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi');
    }
};