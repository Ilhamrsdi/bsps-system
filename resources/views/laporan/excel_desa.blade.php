<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    @verbatim
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Progress Desa</x:Name>
                    <x:WorksheetOptions>
                        <x:DisplayGridlines/>
                    </x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    <![endif]-->
    @endverbatim
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; }
        .kop-title { font-size: 14pt; font-weight: bold; color: #002855; text-align: center; }
        .kop-sub { font-size: 11pt; font-weight: bold; color: #475569; text-align: center; }
        .th-main { background-color: #002855; color: #FFFFFF; font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; padding: 8px; }
        .td-cell { border: 1px solid #cbd5e1; vertical-align: middle; padding: 6px; font-size: 10pt; }
        .td-center { border: 1px solid #cbd5e1; vertical-align: middle; text-align: center; padding: 6px; font-size: 10pt; }
    </style>
</head>
<body>

    <table>
        <tr>
            <td colspan="9" class="kop-title">PEMERINTAH KABUPATEN JEMBER</td>
        </tr>
        <tr>
            <td colspan="9" class="kop-title">DINAS PERUMAHAN RAKYAT, KAWASAN PERMUKIMAN DAN CIPTA KARYA</td>
        </tr>
        <tr>
            <td colspan="9" class="kop-sub">REKAPITULASI PROGRES SURVEI BSPS PER KECAMATAN / DESA</td>
        </tr>
        <tr>
            <td colspan="9" style="text-align:center;font-size:9pt;color:#64748b;">Dicetak Pada: {{ date('d F Y - H:i') }} WIB</td>
        </tr>
        <tr><td colspan="9"></td></tr>
    </table>

    <table border="1" style="border-collapse:collapse;">
        <thead>
            <tr>
                <th class="th-main">Kecamatan</th>
                <th class="th-main">Desa / Kelurahan</th>
                <th class="th-main">Total Usulan</th>
                <th class="th-main">Sudah Survei</th>
                <th class="th-main">Belum Survei</th>
                <th class="th-main">Usulan Baru</th>
                <th class="th-main">Backlog 1</th>
                <th class="th-main">Backlog 2</th>
                <th class="th-main">Progress Survei</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $sumTotal = 0; 
                $sumSudah = 0; 
                $sumBelum = 0; 
                $sumUsulanBaru = 0;
                $sumBacklog1 = 0;
                $sumBacklog2 = 0;
            @endphp
            @foreach($rekapDesaKecamatan as $r)
                @php
                    $b = max(0, $r->total_penerima - $r->total_sudah_survei);
                    $sumTotal += $r->total_penerima;
                    $sumSudah += $r->total_sudah_survei;
                    $sumBelum += $b;
                    $sumUsulanBaru += $r->usulan_baru;
                    $sumBacklog1 += $r->backlog_1;
                    $sumBacklog2 += $r->backlog_2;
                @endphp
                <tr>
                    <td class="td-cell" style="font-weight:bold;">{{ $r->kecamatan }}</td>
                    <td class="td-cell">{{ $r->desa_kelurahan }}</td>
                    <td class="td-center" style="font-weight:bold;">{{ $r->total_penerima }}</td>
                    <td class="td-center">{{ $r->total_sudah_survei }}</td>
                    <td class="td-center">{{ $b }}</td>
                    <td class="td-center">{{ $r->usulan_baru }}</td>
                    <td class="td-center">{{ $r->backlog_1 }}</td>
                    <td class="td-center">{{ $r->backlog_2 }}</td>
                    <td class="td-center" style="font-weight:bold;">{{ $r->progres_survei }}%</td>
                </tr>
            @endforeach
            <tr style="background-color:#e2e8f0;font-weight:bold;">
                <td colspan="2" class="td-cell" style="text-align:right;">TOTAL KESELURUHAN:</td>
                <td class="td-center">{{ $sumTotal }}</td>
                <td class="td-center">{{ $sumSudah }}</td>
                <td class="td-center">{{ $sumBelum }}</td>
                <td class="td-center">{{ $sumUsulanBaru }}</td>
                <td class="td-center">{{ $sumBacklog1 }}</td>
                <td class="td-center">{{ $sumBacklog2 }}</td>
                <td class="td-center">{{ $sumTotal > 0 ? round(($sumSudah/$sumTotal)*100,1) : 0 }}%</td>
            </tr>
        </tbody>
    </table>

</body>
</html>
