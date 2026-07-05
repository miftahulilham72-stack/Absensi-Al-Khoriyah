<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Utama
        User::create([
            'name' => 'Admin Al-Khoeriyah',
            'email' => 'admin@alkhoeriyah.id',
            'password' => Hash::make('admin123'),
        ]);

        // Admin Sekolah
        User::create([
            'name' => 'Admin Sekolah',
            'email' => 'admin@alkhoeriyah.sch.id',
            'password' => Hash::make('admin123'),
        ]);

        // Guru / Staff
        User::create([
            'name' => 'Guru Al-Khoeriyah',
            'email' => 'guru@alkhoeriyah.id',
            'password' => Hash::make('guru123'),
        ]);

        // Staff TU
        User::create([
            'name' => 'Staff TU',
            'email' => 'staff@alkhoeriyah.id',
            'password' => Hash::make('staff123'),
        ]);

        // Kepala Sekolah
        User::create([
            'name' => 'Kepala Sekolah',
            'email' => 'kepsek@alkhoeriyah.id',
            'password' => Hash::make('kepsek123'),
        ]);

        // User biasa untuk testing
        User::create([
            'name' => 'User Testing',
            'email' => 'user@alkhoeriyah.id',
            'password' => Hash::make('user123'),
        ]);
    }
}