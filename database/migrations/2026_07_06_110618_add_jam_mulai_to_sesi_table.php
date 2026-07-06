<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom jam_mulai
        if (!Schema::hasColumn('sesi', 'jam_mulai')) {
            Schema::table('sesi', function (Blueprint $table) {
                $table->time('jam_mulai')->nullable()->after('nama_sesi');
            });
        }

        // Set default jam_mulai = batas_waktu - 1 jam untuk data lama
        DB::statement('UPDATE sesi SET jam_mulai = DATE_SUB(batas_waktu, INTERVAL 1 HOUR) WHERE jam_mulai IS NULL');
        
        // Ubah jam_mulai menjadi NOT NULL
        Schema::table('sesi', function (Blueprint $table) {
            $table->time('jam_mulai')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('sesi', function (Blueprint $table) {
            $table->dropColumn('jam_mulai');
        });
    }
};