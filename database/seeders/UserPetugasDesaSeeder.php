<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\DataPenerima;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserPetugasDesaSeeder extends Seeder
{
    /**
     * Seed 1 Akun Petugas untuk Setiap 1 Desa / Kelurahan se-Kabupaten Jember
     * dan otomatis menghubungkan setiap Data Calon Penerima ke Akun Petugas Desa-nya.
     */
    public function run(): void
    {
        // 1. Akun Admin Utama BSPS Verval
        User::updateOrCreate(
            ['email' => 'admin@bsps.id'],
            [
                'name'      => 'Administrator BSPS Verval',
                'nip'       => '19880315 201502 1 001',
                'jabatan'   => 'Administrator Sistem BSPS Verval',
                'kecamatan' => 'Jember',
                'desa'      => 'Sekretariat BSPS',
                'phone'     => '081234567890',
                'status'    => 'aktif',
                'role'      => 'admin',
                'password'  => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@bsps.jemberkab.go.id'],
            [
                'name'      => 'Admin Kabupaten BSPS',
                'nip'       => '19890412 201801 1 002',
                'jabatan'   => 'Koordinator Data BSPS Kabupaten Jember',
                'kecamatan' => 'Jember',
                'desa'      => 'Dinas Perumahan Rakyat & Kawasan Permukiman',
                'phone'     => '081234567891',
                'status'    => 'aktif',
                'role'      => 'admin',
                'password'  => Hash::make('password'),
            ]
        );

        // Hapus akun lama yang masih memakai domain @pupr.jember.go.id
        User::where('email', 'like', '%@pupr.jember.go.id')->delete();

        // 2. Ambil Semua Daftar Desa / Kelurahan Unik dari Tabel data_penerimas
        $desaList = DataPenerima::select('desa_kelurahan', 'kecamatan')
            ->whereNotNull('desa_kelurahan')
            ->where('desa_kelurahan', '!=', '')
            ->groupBy('desa_kelurahan', 'kecamatan')
            ->orderBy('kecamatan', 'asc')
            ->orderBy('desa_kelurahan', 'asc')
            ->get();

        $defaultPassword = Hash::make('password');
        $usedEmails = [];

        foreach ($desaList as $index => $item) {
            $desaClean = trim($item->desa_kelurahan);
            $kecClean  = trim($item->kecamatan);

            $desaSlug = Str::slug($desaClean, '');
            $kecSlug  = Str::slug($kecClean, '');

            // Format Email: verval.namadesa@gmail.com (atau verval.namadesa.kecamatan@gmail.com jika duplikat nama desa)
            $email = "verval.{$desaSlug}@gmail.com";
            if (in_array($email, $usedEmails)) {
                $email = "verval.{$desaSlug}.{$kecSlug}@gmail.com";
            }
            $usedEmails[] = $email;

            $namaDesaFormatted = ucwords(strtolower($desaClean));
            $namaKecFormatted  = ucwords(strtolower($kecClean));

            $petugas = User::updateOrCreate(
                ['email' => $email],
                [
                    'name'      => "Petugas Verval Desa {$namaDesaFormatted}",
                    'nip'       => null,
                    'jabatan'   => "Petugas Verval Desa {$namaDesaFormatted}",
                    'kecamatan' => $namaKecFormatted,
                    'desa'      => $namaDesaFormatted,
                    'phone'     => null,
                    'status'    => 'aktif',
                    'role'      => 'petugas',
                    'password'  => $defaultPassword,
                ]
            );

            // 3. Hubungkan seluruh data penerima di desa ini ke akun petugas tersebut
            DataPenerima::where('desa_kelurahan', $desaClean)
                ->update(['user_id' => $petugas->id]);
        }
    }
}
