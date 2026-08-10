<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DataPenerimaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\DataPenerima::truncate();

        $json = file_get_contents(storage_path('app/12_ribu.json'));
        $data = json_decode($json, true);
        
        $chunks = array_chunk($data, 1000);
        foreach ($chunks as $chunk) {
            \App\Models\DataPenerima::insert($chunk);
        }
    }
}
