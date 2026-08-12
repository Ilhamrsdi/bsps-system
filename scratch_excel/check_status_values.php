<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$kec = 'JOMBANG';
$query = \App\Models\DataPenerima::whereRaw('LOWER(TRIM(kecamatan)) = ?', [strtolower($kec)]);

$statusCounts = (clone $query)->selectRaw('status, status_kelayakan, count(*) as total')
    ->groupBy('status', 'status_kelayakan')
    ->get();

echo "=== Status Breakdown di Kecamatan Jombang ===" . PHP_EOL;
foreach ($statusCounts as $s) {
    echo "Status: '" . ($s->status ?: 'NULL') . "' | Status Kelayakan: '" . ($s->status_kelayakan ?: 'NULL') . "' | Count: {$s->total}" . PHP_EOL;
}
