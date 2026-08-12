<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = \App\Models\DataPenerima::selectRaw("
    kecamatan,
    desa_kelurahan,
    COUNT(*) as total_penerima,
    SUM(CASE WHEN (foto_sudut_depan IS NOT NULL AND foto_sudut_depan != '') THEN 1 ELSE 0 END) as total_sudah_survei_foto,
    SUM(CASE WHEN status_kelayakan IS NOT NULL AND status_kelayakan != '' THEN 1 ELSE 0 END) as total_sudah_survei_status,
    SUM(CASE WHEN status_kelayakan = 'Layak Diusulkan' THEN 1 ELSE 0 END) as total_layak,
    SUM(CASE WHEN status_kelayakan = 'Tidak Layak Diusulkan' THEN 1 ELSE 0 END) as total_tidak_layak
")
->groupBy('kecamatan', 'desa_kelurahan')
->having('total_sudah_survei_status', '>', 0)
->limit(10)
->get();

foreach ($rows as $r) {
    $pct_vs_foto = $r->total_sudah_survei_foto > 0 ? round(($r->total_layak / $r->total_sudah_survei_foto) * 100, 1) : 0;
    $pct_vs_status = $r->total_sudah_survei_status > 0 ? round(($r->total_layak / $r->total_sudah_survei_status) * 100, 1) : 0;
    $pct_vs_total = $r->total_penerima > 0 ? round(($r->total_layak / $r->total_penerima) * 100, 1) : 0;

    echo "Kec: {$r->kecamatan} | Desa: {$r->desa_kelurahan}" . PHP_EOL;
    echo "  Total Target: {$r->total_penerima}" . PHP_EOL;
    echo "  Survei Status: {$r->total_sudah_survei_status} | Layak: {$r->total_layak} | Tidak Layak: {$r->total_tidak_layak}" . PHP_EOL;
    echo "  Survei Foto: {$r->total_sudah_survei_foto}" . PHP_EOL;
    echo "  % Layak vs Total Target: {$pct_vs_total}%" . PHP_EOL;
    echo "  % Layak vs Sudah Survei (Status): {$pct_vs_status}%" . PHP_EOL;
    echo "  % Layak vs Sudah Survei (Foto): {$pct_vs_foto}%" . PHP_EOL;
    echo "----------------------------------------------------" . PHP_EOL;
}
