<?php

namespace Database\Seeders;

use App\Models\KepalaDesa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class KepalaDesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = storage_path('app/kepala_desa.json');
        
        if (!File::exists($jsonPath)) {
            $this->command->error("File {$jsonPath} tidak ditemukan!");
            return;
        }

        $data = json_decode(File::get($jsonPath), true);
        if (empty($data)) {
            $this->command->error("Data kepala_desa.json kosong!");
            return;
        }

        KepalaDesa::truncate();

        $rows = [];
        $now = now();

        foreach ($data as $item) {
            $rows[] = [
                'kecamatan'      => $item['kecamatan'] ?? '',
                'desa_kelurahan' => $item['desa'] ?? '',
                'jabatan'        => $item['jabatan'] ?? 'KEPALA DESA',
                'nama'           => $item['nama'] ?? '',
                'nomor_telepon'  => $item['telp'] ?? null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ];
        }

        foreach (array_chunk($rows, 100) as $chunk) {
            KepalaDesa::insert($chunk);
        }

        $this->command->info("Berhasil mengimpor " . count($rows) . " data Kepala Desa ke database MySQL!");
    }
}
