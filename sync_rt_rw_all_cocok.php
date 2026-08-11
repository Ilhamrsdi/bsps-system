<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DataPenerima;
use Illuminate\Support\Facades\DB;

echo "--- SYNC RT/RW & DUSUN UNTUK SELURUH DATA PENERIMA COCOK --- \n";

$start = microtime(true);
$updatedCount = 0;

DataPenerima::select('id', 'nama', 'no_ktp', 'alamat', 'dusun', 'rt', 'rw')
    ->chunkById(500, function ($items) use (&$updatedCount) {
        $niks = $items->pluck('no_ktp')->map(fn($v) => trim($v))->filter()->unique()->toArray();
        if (empty($niks)) return;

        $rows = DB::connection('dataguse')
            ->table('data_penduduks')
            ->whereIn('nomor_induk_kependudukan', $niks)
            ->select('nomor_induk_kependudukan as nik', 'alamat', 'dusun', 'rt', 'rw')
            ->get();

        $dgMap = [];
        foreach ($rows as $r) {
            $dgMap[trim($r->nik)] = $r;
        }

        foreach ($items as $item) {
            $nik = trim($item->no_ktp ?? '');
            if (!$nik || !isset($dgMap[$nik])) continue;

            $dp = $dgMap[$nik];
            $up = [];

            // 1. Extract Dusun
            $dgDusun = trim($dp->dusun ?? '');
            if ($dgDusun && !$item->dusun) {
                $up['dusun'] = $dgDusun;
            }

            // 2. Extract RT & RW
            $rt = trim($dp->rt ?? '');
            $rw = trim($dp->rw ?? '');

            // Parsing fallback dari rt_rw / alamat jika rt/rw kosong
            if (!$rt || !$rw) {
                $searchStr = ($dp->rt_rw ?? '') . ' ' . ($dp->alamat ?? '');
                if (!$rt && preg_match('/(?:rt|r\.t)\.?\s*0*(\d+)/i', $searchStr, $mRt)) {
                    $rt = $mRt[1];
                }
                if (!$rw && preg_match('/(?:rw|r\.w)\.?\s*0*(\d+)/i', $searchStr, $mRw)) {
                    $rw = $mRw[1];
                }
            }

            if ($rt) $up['rt'] = $rt;
            if ($rw) $up['rw'] = $rw;

            // 3. Gabungkan RT/RW ke alamat lokal jika belum ada label RT
            $currentAlamat = trim($item->alamat ?? '');
            if (($rt || $rw) && !preg_match('/rt\s*\d+/i', $currentAlamat)) {
                $rtLabel = $rt ? "RT" . str_pad($rt, 3, '0', STR_PAD_LEFT) : "";
                $rwLabel = $rw ? "RW" . str_pad($rw, 3, '0', STR_PAD_LEFT) : "";
                $rtrwTag = trim("{$rtLabel} {$rwLabel}");
                if ($rtrwTag) {
                    $up['alamat'] = $currentAlamat ? "{$currentAlamat} {$rtrwTag}" : $rtrwTag;
                }
            }

            if (!empty($up)) {
                $item->update($up);
                $updatedCount++;
            }
        }

        echo "Updated RT/RW for {$updatedCount} records...\n";
    });

$elapsed = round(microtime(true) - $start, 2);
echo "\n=============================================\n";
echo "SELESAI SINKRONISASI RT/RW SELURUH DATA!\n";
echo "Total Records Diperbarui : {$updatedCount}\n";
echo "Waktu Execution         : {$elapsed} detik\n";
echo "=============================================\n";
