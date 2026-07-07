<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PanitiaSeeder extends Seeder
{
    public function run(): void
    {
        // Buat akun panitia jika belum ada
        User::updateOrCreate(
            ['email' => 'panitia@alkhoeriyah.sch.id'],
            [
                'name' => 'Panitia Lapangan',
                'password' => Hash::make('kiosk123'),
                'role' => 'operator',
            ]
        );

        // Buat akun admin jika belum ada
        User::updateOrCreate(
            ['email' => 'admin@alkhoeriyah.sch.id'],
            [
                'name' => 'Admin Al-Khoeriyah',
                'password' => Hash::make('login'),
                'role' => 'admin',
            ]
        );

        $this->command->info('✅ Akun Admin dan Panitia berhasil dibuat!');
        $this->command->info('📧 Admin: admin@alkhoeriyah.sch.id | Password: login');
        $this->command->info('📧 Panitia: panitia@alkhoeriyah.sch.id | Password: kiosk123');
    }
}