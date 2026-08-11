<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$users = User::where('role', 'admin_kecamatan')->orderBy('kecamatan', 'asc')->get();

$filenameCsv = public_path('Akun_Admin_Kecamatan_BSPS.csv');
$filenameXls = public_path('Akun_Admin_Kecamatan_BSPS.xls');

// 1. Generate CSV File (UTF-8 BOM)
$fp = fopen($filenameCsv, 'w');
fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8 for Excel
fputcsv($fp, ['No', 'Nama Akun', 'Email / Username Login', 'Password', 'Kecamatan', 'Jabatan', 'Status Akun']);

$no = 1;
foreach ($users as $u) {
    fputcsv($fp, [
        $no++,
        $u->name,
        $u->email,
        'password123',
        $u->kecamatan,
        $u->jabatan,
        $u->status,
    ]);
}
fclose($fp);

// 2. Generate Excel HTML (.xls) for rich formatting when opened in Microsoft Excel
$html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Daftar Akun Admin Kecamatan</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->
<style>
    th { background-color: #002855; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #000000; padding: 8px; }
    td { border: 1px solid #cccccc; padding: 6px 10px; font-family: Arial, sans-serif; font-size: 11pt; }
    .center { text-align: center; }
    .bold { font-weight: bold; }
</style>
</head>
<body>
<h2>DAFTAR AKUN ADMIN KECAMATAN BSPS KABUPATEN JEMBER</h2>
<p>Tanggal Export: ' . date('d F Y H:i:s') . '</p>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Akun</th>
            <th>Email / Username Login</th>
            <th>Password</th>
            <th>Kecamatan</th>
            <th>Jabatan</th>
            <th>Status Akun</th>
        </tr>
    </thead>
    <tbody>';

$no = 1;
foreach ($users as $u) {
    $html .= '<tr>
        <td class="center">' . $no++ . '</td>
        <td class="bold">' . htmlspecialchars($u->name) . '</td>
        <td>' . htmlspecialchars($u->email) . '</td>
        <td class="center bold" style="color: #004080;">password123</td>
        <td class="center bold">' . htmlspecialchars($u->kecamatan) . '</td>
        <td>' . htmlspecialchars($u->jabatan) . '</td>
        <td class="center">' . htmlspecialchars(ucfirst($u->status)) . '</td>
    </tr>';
}

$html .= '</tbody></table></body></html>';

file_put_contents($filenameXls, $html);

echo "SUCCESS! Created CSV & XLS files:\n";
echo "CSV: " . $filenameCsv . "\n";
echo "XLS: " . $filenameXls . "\n";
