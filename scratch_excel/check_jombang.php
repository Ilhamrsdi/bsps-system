<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$kec = 'JOMBANG';
$query = \App\Models\DataPenerima::whereRaw('LOWER(TRIM(kecamatan)) = ?', [strtolower($kec)]);

$conds = [];
foreach (\App\Models\DataPenerima::$fieldWajibSurvei as $field) {
    $conds[] = "({$field} IS NOT NULL AND TRIM({$field}) != '')";
}
$formLengkapSql = "(" . implode(" AND ", $conds) . ")";
$sudahSql = "(status IN ('meninggal', 'pindah', 'tidak diketahui') OR {$formLengkapSql})";

$totalPenerima = (clone $query)->count();
$totalSudahSurvei = (clone $query)->whereRaw($sudahSql)->count();
$totalBelumSurvei = (clone $query)->whereRaw("NOT {$sudahSql}")->count();
$totalLayak = (clone $query)->where('status_kelayakan', 'Layak Diusulkan')->count();
$totalTidakLayak = (clone $query)->where('status_kelayakan', 'Tidak Layak Diusulkan')->count();

$progressPercent = $totalPenerima > 0 ? round(($totalSudahSurvei / $totalPenerima) * 100, 1) : 0;
$kesesuaianPercent = $totalSudahSurvei > 0 ? round(($totalLayak / $totalSudahSurvei) * 100, 1) : 0;

echo "=== Rincian Baru Data Kecamatan Jombang ===" . PHP_EOL;
echo "Total Target Penerima: {$totalPenerima}" . PHP_EOL;
echo "Sudah Disurvei / Diverifikasi: {$totalSudahSurvei}" . PHP_EOL;
echo "Belum Disurvei: {$totalBelumSurvei}" . PHP_EOL;
echo "Hasil Sesuai (Layak Diusulkan): {$totalLayak}" . PHP_EOL;
echo "Hasil Tidak Sesuai (Tidak Layak): {$totalTidakLayak}" . PHP_EOL;
echo "% Progres Survei Lapangan: {$progressPercent}%" . PHP_EOL;
echo "% Kesesuaian Hasil (Layak vs Survei): {$kesesuaianPercent}%" . PHP_EOL;
echo PHP_EOL . "--- Breakdown per Desa di Kecamatan Jombang ---" . PHP_EOL;

$desas = \App\Models\DataPenerima::selectRaw("
        desa_kelurahan,
        COUNT(*) as total,
        SUM(CASE WHEN {$sudahSql} THEN 1 ELSE 0 END) as sudah,
        SUM(CASE WHEN status_kelayakan = 'Layak Diusulkan' THEN 1 ELSE 0 END) as layak,
        SUM(CASE WHEN status_kelayakan = 'Tidak Layak Diusulkan' THEN 1 ELSE 0 END) as tidak_layak
    ")
    ->whereRaw('LOWER(TRIM(kecamatan)) = ?', [strtolower($kec)])
    ->groupBy('desa_kelurahan')
    ->orderBy('desa_kelurahan')
    ->get();

foreach ($desas as $d) {
    $b = max(0, $d->total - $d->sudah);
    $pctS = $d->total > 0 ? round(($d->sudah / $d->total) * 100, 1) : 0;
    $pctL = $d->sudah > 0 ? round(($d->layak / $d->sudah) * 100, 1) : 0;
    echo "Desa {$d->desa_kelurahan}: Total={$d->total} | Sudah={$d->sudah} | Belum={$b} | Layak={$d->layak} | TidakLayak={$d->tidak_layak} | %Progres={$pctS}% | %Kesesuaian={$pctL}%" . PHP_EOL;
}
