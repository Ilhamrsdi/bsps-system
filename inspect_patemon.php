<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\DataPenerima;

echo "--- CEK AKUN USER PATEMON ---\n";
$users = User::where('email', 'like', '%patemon%')->orWhere('name', 'like', '%patemon%')->get();
foreach ($users as $u) {
    echo "ID: {$u->id} | Name: {$u->name} | Email: {$u->email} | Kec: {$u->kecamatan} | Desa: {$u->desa} | Role: {$u->role}\n";
}

echo "\n--- CEK DATA PENERIMA DENGAN DESA PATEMON ---\n";
$penerimas = DataPenerima::where('desa_kelurahan', 'like', '%patemon%')->select('kecamatan', 'desa_kelurahan')->distinct()->get();
foreach ($penerimas as $dp) {
    $count = DataPenerima::where('desa_kelurahan', $dp->desa_kelurahan)->where('kecamatan', $dp->kecamatan)->count();
    echo "Kecamatan: {$dp->kecamatan} | Desa: {$dp->desa_kelurahan} | Jumlah Penerima: {$count}\n";
}

echo "\n--- DESA DENGAN NAMA SAMA DI LEBIH DARI 1 KECAMATAN ---\n";
$duplicateDesa = DataPenerima::select('desa_kelurahan')
    ->groupBy('desa_kelurahan')
    ->havingRaw('COUNT(DISTINCT kecamatan) > 1')
    ->pluck('desa_kelurahan');

foreach ($duplicateDesa as $desa) {
    $kecamatans = DataPenerima::where('desa_kelurahan', $desa)->distinct()->pluck('kecamatan')->toArray();
    echo "Desa '{$desa}' ada di kecamatan: " . implode(', ', $kecamatans) . "\n";
}
