<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            // Cek apakah kolom sudah ada sebelum menambahkan
            if (!Schema::hasColumn('absensi', 'keterangan')) {
                $table->enum('keterangan', ['Hadir', 'Sakit', 'Izin', 'Alpa'])->default('Hadir')->after('status');
            }
            
            if (!Schema::hasColumn('absensi', 'absen_manual')) {
                $table->boolean('absen_manual')->default(false)->after('ttd_image');
            }
            
            if (!Schema::hasColumn('absensi', 'diabsensi_oleh')) {
                $table->string('diabsensi_oleh', 100)->nullable()->after('absen_manual');
            }
        });
    }

    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->dropColumn(['keterangan', 'absen_manual', 'diabsensi_oleh']);
        });
    }
};