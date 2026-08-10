<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$users = DB::table('users')->orderBy('role')->orderBy('desa')->get();

$csvHeader = ["No", "Nama User", "Username / Email", "Password Default", "Desa / Kelurahan", "Kecamatan", "Role", "No HP"];
$rows = [];
$rows[] = implode(",", array_map(function($val) { return '"' . str_replace('"', '""', $val) . '"'; }, $csvHeader));

$i = 1;
foreach ($users as $u) {
    $rows[] = implode(",", [
        $i++,
        '"' . str_replace('"', '""', $u->name ?? '') . '"',
        '"' . str_replace('"', '""', $u->email ?? '') . '"',
        '"password"',
        '"' . str_replace('"', '""', $u->desa ?? '') . '"',
        '"' . str_replace('"', '""', $u->kecamatan ?? '') . '"',
        '"' . str_replace('"', '""', $u->role ?? '') . '"',
        '"' . str_replace('"', '""', $u->phone ?? '') . '"',
    ]);
}

$csvContent = chr(0xEF) . chr(0xBB) . chr(0xBF) . implode("\n", $rows);

file_put_contents(__DIR__ . '/export_users_petugas.csv', $csvContent);
file_put_contents(__DIR__ . '/public/export_users_petugas.csv', $csvContent);

// Also generate HTML Excel format (.xls) which opens cleanly in Excel
$htmlExcel = '<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  th { background-color: #002855; color: #ffffff; font-weight: bold; border: 1px solid #000000; padding: 6px 12px; }
  td { border: 1px solid #cccccc; padding: 6px 12px; }
</style>
</head>
<body>
<h2>Data Akun Petugas & User BSPS Verval - Production</h2>
<table>
<thead>
<tr>
  <th>No</th>
  <th>Nama User</th>
  <th>Username / Email</th>
  <th>Password Default</th>
  <th>Desa / Kelurahan</th>
  <th>Kecamatan</th>
  <th>Role</th>
  <th>No HP</th>
</tr>
</thead>
<tbody>';

$no = 1;
foreach ($users as $u) {
    $htmlExcel .= '<tr>
      <td>' . $no++ . '</td>
      <td>' . htmlspecialchars($u->name ?? '') . '</td>
      <td>' . htmlspecialchars($u->email ?? '') . '</td>
      <td>password</td>
      <td>' . htmlspecialchars($u->desa ?? '') . '</td>
      <td>' . htmlspecialchars($u->kecamatan ?? '') . '</td>
      <td>' . htmlspecialchars($u->role ?? '') . '</td>
      <td>' . htmlspecialchars($u->phone ?? '') . '</td>
    </tr>';
}

$htmlExcel .= '</tbody></table></body></html>';

file_put_contents(__DIR__ . '/export_users_petugas.xls', $htmlExcel);
file_put_contents(__DIR__ . '/public/export_users_petugas.xls', $htmlExcel);

echo "BERHASIL EKSPOR DATA REAL PRODUCTION:\n";
echo "- Total User Expor: " . count($users) . " akun\n";
echo "- File CSV: export_users_petugas.csv\n";
echo "- File Excel: export_users_petugas.xls\n";
