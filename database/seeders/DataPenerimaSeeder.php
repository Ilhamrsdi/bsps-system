<?php

namespace Database\Seeders;

use App\Models\DataPenerima;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DataPenerimaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kosongkan tabel terlebih dahulu agar tidak duplikat
        Schema::disableForeignKeyConstraints();
        DataPenerima::truncate();
        Schema::enableForeignKeyConstraints();

        $jsonPath = storage_path('app/12_ribu.json');
        if (!file_exists($jsonPath)) {
            $jsonPath = base_path('12_ribu.json');
        }

        $json = file_get_contents($jsonPath);
        $data = json_decode($json, true);
        
        $chunks = array_chunk($data, 1000);
        foreach ($chunks as $chunk) {
            DataPenerima::insert($chunk);
        }
    }
}
