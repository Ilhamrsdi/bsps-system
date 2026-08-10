<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with ONLY 1 Admin and 2 Petugas.
     */
    public function run(): void
    {
        // 1. Akun Admin Utama (1 Admin)
        User::updateOrCreate(
            ['email' => 'admin@pupr.jember.go.id'],
            [
                'name'      => 'Admin Utama PUPR',
                'nip'       => '19880315 201502 1 001',
                'jabatan'   => 'Administrator Sistem PUPR',
                'kecamatan' => 'Kaliwates',
                'phone'     => '081234567890',
                'status'    => 'aktif',
                'role'      => 'admin',
                'password'  => Hash::make('password'),
            ]
        );

        // 2. Akun Petugas 1 (Budi Santoso)
        User::updateOrCreate(
            ['email' => 'budi@pupr.jember.go.id'],
            [
                'name'      => 'Budi Santoso',
                'nip'       => '19900512 201802 1 005',
                'jabatan'   => 'Petugas Survei Lapangan',
                'kecamatan' => 'Kaliwates',
                'phone'     => '081987654321',
                'status'    => 'aktif',
                'role'      => 'petugas',
                'password'  => Hash::make('password'),
            ]
        );

        // 3. Akun Petugas 2 (Agus Wijaya)
        User::updateOrCreate(
            ['email' => 'agus@pupr.jember.go.id'],
            [
                'name'      => 'Agus Wijaya',
                'nip'       => '19920820 201903 1 008',
                'jabatan'   => 'Petugas Survei Lapangan',
                'kecamatan' => 'Sumbersari',
                'phone'     => '081223344556',
                'status'    => 'aktif',
                'role'      => 'petugas',
                'password'  => Hash::make('password'),
            ]
        );
    }
}
