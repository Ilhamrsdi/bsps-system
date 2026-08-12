<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$conds = [];
foreach (\App\Models\DataPenerima::$fieldWajibSurvei as $field) {
    $conds[] = "({$field} IS NOT NULL AND TRIM({$field}) != '')";
}
$formLengkapSql = "(" . implode(" AND ", $conds) . ")";

// Pilihan 1: sql lama
$sudahSql1 = "(status IN ('meninggal', 'pindah', 'tidak diketahui') OR {$formLengkapSql})";

// Pilihan 2: COALESCE / NULL-safe sql
$condsSafe = [];
foreach (\App\Models\DataPenerima::$fieldWajibSurvei as $field) {
    $condsSafe[] = "(COALESCE({$field}, '') != '')";
}
$formLengkapSafeSql = "(" . implode(" AND ", $condsSafe) . ")";
$sudahSql2 = "((status IS NOT NULL AND status IN ('meninggal', 'pindah', 'tidak diketahui')) OR {$formLengkapSafeSql})";

$kec = 'JOMBANG';
$rows1 = \App\Models\DataPenerima::selectRaw("
        desa_kelurahan,
        COUNT(*) as total,
        SUM(CASE WHEN {$sudahSql1} THEN 1 ELSE 0 END) as sudah1,
        SUM(CASE WHEN NOT {$sudahSql1} THEN 1 ELSE 0 END) as belum1_not,
        SUM(CASE WHEN NOT ({$sudahSql1}) OR ({$sudahSql1}) IS NULL THEN 1 ELSE 0 END) as belum1_safe
    ")
    ->whereRaw('LOWER(TRIM(kecamatan)) = ?', [strtolower($kec)])
    ->groupBy('desa_kelurahan')
    ->get();

echo "=== Hasil Test SQL NULL 3-Valued Logic ===" . PHP_EOL;
foreach ($rows1 as $r) {
    echo "Desa: {$r->desa_kelurahan} | Total: {$r->total} | Sudah: {$r->sudah1} | Belum NOT: {$r->belum1_not} | Belum SAFE: {$r->belum1_safe}" . PHP_EOL;
}
