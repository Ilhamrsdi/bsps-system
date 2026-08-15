<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DataPenerima;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

$outputPath = storage_path('app/public/export_photos_by_nik');

if (!File::exists($outputPath)) {
    File::makeDirectory($outputPath, 0755, true);
}

$niksJsonPath = 'C:\\Users\\ilham\\.gemini\\antigravity-ide\\brain\\ce54535e-ff3a-48cf-9418-dc874f725cc9\\scratch\\excel_extract\\niks.json';
if (!File::exists($niksJsonPath)) {
    die("niks.json not found!");
}

$niks = json_decode(file_get_contents($niksJsonPath), true);
if (empty($niks)) {
    die("No NIKs found in JSON");
}

echo "Found " . count($niks) . " NIKs to process.\n";

$penerimas = DataPenerima::whereIn('no_ktp', $niks)->get();
echo "Found " . $penerimas->count() . " records in DB.\n";

$photoFields = [
    'foto_sudut_depan',
    'foto_sudut_belakang',
    'foto_bagian_dalam',
    'foto_sudut_kiri',
    'foto_sudut_kanan',
    'ktp',
    'kk',
    'surat_pernyataan',
    'sertifikat_tanah' // Ditambahkan sesuai permintaan
];

$count = 0;
foreach ($penerimas as $p) {
    $nikDir = $outputPath . '/' . $p->no_ktp;
    $hasPhoto = false;

    foreach ($photoFields as $field) {
        $photoPath = $p->$field;
        
        if (!empty($photoPath)) {
            // Daftar kemungkinan lokasi file (termasuk shared hosting public_html)
            $possiblePaths = [
                public_path($photoPath),
                storage_path('app/public/'.$photoPath),
                __DIR__.'/public/'.$photoPath,
                __DIR__.'/public_html/public/'.$photoPath,
                __DIR__.'/public_html/'.$photoPath
            ];
            
            $actualPath = null;
            foreach ($possiblePaths as $pathToCheck) {
                if (File::exists($pathToCheck)) {
                    $actualPath = $pathToCheck;
                    break;
                }
            }

            if ($actualPath) {
                if (!$hasPhoto) {
                    if (!File::exists($nikDir)) {
                        File::makeDirectory($nikDir, 0755, true);
                    }
                    $hasPhoto = true;
                }
                
                $ext = pathinfo($photoPath, PATHINFO_EXTENSION);
                if (empty($ext)) $ext = 'jpg';
                $dest = $nikDir . '/' . $field . '.' . $ext;
                
                File::copy($actualPath, $dest);
                $count++;
            }
        }
    }
}

echo "Extracted $count photos to $outputPath\n";
