<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DataPenerima;
use Illuminate\Support\Facades\DB;

echo "--- POPULATING DG_STATUS FOR ALL 12.673 DATA PENERIMA --- \n";

$start = microtime(true);
$processed = 0;
$cocok = 0;
$beda = 0;
$tidak = 0;

$cStr = fn($s) => mb_strtolower(trim(preg_replace('/\s+/', ' ', $s ?? '')));
$cKab = fn($s) => trim(preg_replace('/^(kab\.|kabupaten|kota)\s+/i', '', $cStr($s)));
$cAlamat = fn($s) => trim(preg_replace('/(\(|\,)?\s*rt\s*\d+.*$/i', '', $cStr($s)));

DataPenerima::select('id', 'nama', 'no_ktp', 'alamat', 'dusun', 'desa_kelurahan', 'kecamatan', 'kabupaten_kota')
    ->chunkById(500, function ($items) use (&$processed, &$cocok, &$beda, &$tidak, $cStr, $cKab, $cAlamat) {
        $niks = $items->pluck('no_ktp')->map(fn($v) => trim($v))->filter()->unique()->toArray();
        
        $dgMap = [];
        if (!empty($niks)) {
            $rows = DB::connection('dataguse')
                ->table('data_penduduks')
                ->whereIn('nomor_induk_kependudukan', $niks)
                ->select('nomor_induk_kependudukan as nik', 'nama', 'alamat', 'dusun', 'desa_kelurahan', 'kecamatan', 'kabupaten')
                ->get();
            foreach ($rows as $r) {
                $dgMap[trim($r->nik)] = $r;
            }
        }

        foreach ($items as $item) {
            $nik = trim($item->no_ktp ?? '');
            if (!$nik || !isset($dgMap[$nik])) {
                $item->update(['dg_status' => 'tidak_ditemukan']);
                $tidak++;
                $processed++;
                continue;
            }

            $dp = $dgMap[$nik];
            $diffs = 0;

            if ($dp->nama && $cStr($item->nama) !== $cStr($dp->nama)) $diffs++;
            
            if ($dp->alamat) {
                $la = $cAlamat($item->alamat);
                $da = $cAlamat($dp->alamat);
                if ($la !== $da && !str_contains($da, $la) && !str_contains($la, $da)) $diffs++;
            }

            if ($dp->dusun) {
                $ld = $cStr($item->dusun);
                $dd = $cStr($dp->dusun);
                $la = $cStr($item->alamat);
                if ($ld !== $dd && !str_contains($la, $dd)) $diffs++;
            }

            if ($dp->desa_kelurahan && $cStr($item->desa_kelurahan) !== $cStr($dp->desa_kelurahan)) $diffs++;
            if ($dp->kecamatan && $cStr($item->kecamatan) !== $cStr($dp->kecamatan)) $diffs++;
            if ($dp->kabupaten && $cKab($item->kabupaten_kota) !== $cKab($dp->kabupaten)) $diffs++;

            $st = ($diffs === 0) ? 'cocok' : 'beda';
            if ($st === 'cocok') $cocok++; else $beda++;
            
            $item->update(['dg_status' => $st]);
            $processed++;
        }

        echo "Processed {$processed} / 12673 data...\n";
    });

$elapsed = round(microtime(true) - $start, 2);
echo "\n=============================================\n";
echo "SELESAI POPULASI STATISTIK KESELURUHAN DATA!\n";
echo "Total Processed : {$processed}\n";
echo "Cocok Sempurna  : {$cocok}\n";
echo "Beda Data       : {$beda}\n";
echo "Tidak Ditemukan : {$tidak}\n";
echo "Waktu           : {$elapsed} detik\n";
echo "=============================================\n";
