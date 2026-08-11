<?php

namespace Database\Seeders;

use App\Models\DataPenerima;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminKecamatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kecList = DataPenerima::distinct('kecamatan')
            ->whereNotNull('kecamatan')
            ->where('kecamatan', '!=', '')
            ->orderBy('kecamatan', 'asc')
            ->pluck('kecamatan');

        if ($kecList->isEmpty()) {
            $kecList = collect(['AJUNG', 'AMBULU', 'ARJASA', 'BALUNG', 'BANGSALSARI', 'JENGGAWAH', 'KALIWATES', 'PATRANG', 'SUMBERSARI']);
        }

        $created = 0;
        foreach ($kecList as $k) {
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $k));
            $email = 'admin.' . $slug . '@gmail.com';
            $name = 'Admin Kec. ' . ucwords(strtolower($k));

            User::updateOrCreate(
                ['email' => $email],
                [
                    'name'           => $name,
                    'password'       => Hash::make('password'),
                    'plain_password' => 'password',
                    'role'           => 'admin_kecamatan',
                    'kecamatan' => strtoupper($k),
                    'jabatan'   => 'Admin Kecamatan ' . ucwords(strtolower($k)),
                    'status'    => 'aktif',
                ]
            );
            $created++;
        }

        $this->command->info("Berhasil membuat/memperbarui {$created} akun Admin Kecamatan di database!");
    }
}
