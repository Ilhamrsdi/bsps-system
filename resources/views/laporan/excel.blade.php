<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    @verbatim
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Rekap BSPS Verval</x:Name>
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
        .th-sub { background-color: #0f172a; color: #FFFFFF; font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; padding: 6px; }
        .td-cell { border: 1px solid #cbd5e1; vertical-align: middle; padding: 6px; font-size: 10pt; }
        .td-center { border: 1px solid #cbd5e1; vertical-align: middle; text-align: center; padding: 6px; font-size: 10pt; }
        .stat-box { border: 1px solid #002855; background-color: #f8fafc; font-weight: bold; text-align: center; padding: 8px; }
        .badge-layak { background-color: #dcfce7; color: #166534; font-weight: bold; text-align: center; }
        .badge-tidak { background-color: #fee2e2; color: #991b1b; font-weight: bold; text-align: center; }
        .badge-belum { background-color: #fef3c7; color: #92400e; font-weight: bold; text-align: center; }
    </style>
</head>
<body>

    <!-- KOP SURAT PROGRAM BSPS -->
    <table>
        <tr>
            <td colspan="13" class="kop-title">PEMERINTAH KABUPATEN JEMBER</td>
        </tr>
        <tr>
            <td colspan="13" class="kop-title">DINAS PERUMAHAN RAKYAT, KAWASAN PERMUKIMAN DAN CIPTA KARYA</td>
        </tr>
        <tr>
            <td colspan="13" class="kop-sub">PROGRAM BANTUAN STIMULAN PERUMAHAN SWADAYA (BSPS) VERVAL</td>
        </tr>
        <tr>
            <td colspan="13" style="text-align:center;font-size:9pt;color:#64748b;">Dicetak Pada: {{ date('d F Y - H:i') }} WIB | Sistem Informasi BSPS Verval Kabupaten Jember</td>
        </tr>
        <tr><td colspan="13"></td></tr>
    </table>

    <!-- RINGKASAN STATISTIK -->
    <table border="1" style="border-collapse:collapse;margin-bottom:15px;">
        <tr style="background-color:#e2e8f0;font-weight:bold;">
            <td class="stat-box">TOTAL PENERIMA</td>
            <td class="stat-box">SUDAH SURVEI</td>
            <td class="stat-box">BELUM SURVEI</td>
            <td class="stat-box" style="color:#166534;">HASIL SESUAI (LAYAK)</td>
            <td class="stat-box" style="color:#991b1b;">HASIL TIDAK SESUAI</td>
        </tr>
        <tr>
            <td class="td-center" style="font-size:12pt;font-weight:bold;">{{ number_format($stats['total']) }}</td>
            <td class="td-center" style="font-size:12pt;font-weight:bold;">{{ number_format($stats['sudah']) }}</td>
            <td class="td-center" style="font-size:12pt;font-weight:bold;color:#b78100;">{{ number_format($stats['belum']) }}</td>
            <td class="td-center" style="font-size:12pt;font-weight:bold;color:#166534;">{{ number_format($stats['layak']) }}</td>
            <td class="td-center" style="font-size:12pt;font-weight:bold;color:#991b1b;">{{ number_format($stats['tidak_layak']) }}</td>
        </tr>
    </table>

    <br />

    <!-- TABEL 1: REKAP AGREGAT PER DESA & KECAMATAN -->
    <h3 style="color:#002855;margin-bottom:5px;">I. REKAPITULASI HASIL VERIFIKASI (SESUAI / TIDAK SESUAI) PER DESA &amp; KECAMATAN</h3>
    <table border="1" style="border-collapse:collapse;">
        <thead>
            <tr>
                <th class="th-main" style="width:40px;">NO</th>
                <th class="th-main" style="width:160px;">KECAMATAN</th>
                <th class="th-main" style="width:160px;">DESA / KELURAHAN</th>
                <th class="th-main" style="width:110px;">TOTAL TARGET</th>
                <th class="th-main" style="width:110px;">SUDAH SURVEI</th>
                <th class="th-main" style="width:110px;">BELUM SURVEI</th>
                <th class="th-main" style="width:140px;">HASIL SESUAI (LAYAK)</th>
                <th class="th-main" style="width:140px;">HASIL TIDAK SESUAI</th>
                <th class="th-main" style="width:120px;">% PROGRES SURVEI</th>
                <th class="th-main" style="width:130px;">% KESESUAIAN HASIL</th>
            </tr>
        </thead>
        <tbody>
            @php $sumTotal=0; $sumSudah=0; $sumBelum=0; $sumLayak=0; $sumTidak=0; @endphp
            @foreach($rekapDesaKecamatan as $idx => $r)
                @php
                    $b = max(0, $r->total_penerima - $r->total_sudah_survei);
                    $pctSurvei = $r->total_penerima > 0 ? round(($r->total_sudah_survei / $r->total_penerima)*100, 1) : 0;
                    $pctKesesuaian = $r->total_sudah_survei > 0 ? round(($r->total_layak / $r->total_sudah_survei)*100, 1) : 0;
                    $sumTotal += $r->total_penerima;
                    $sumSudah += $r->total_sudah_survei;
                    $sumBelum += $b;
                    $sumLayak += $r->total_layak;
                    $sumTidak += $r->total_tidak_layak;
                @endphp
                <tr>
                    <td class="td-center">{{ $idx + 1 }}</td>
                    <td class="td-cell" style="font-weight:bold;">{{ $r->kecamatan }}</td>
                    <td class="td-cell">{{ $r->desa_kelurahan }}</td>
                    <td class="td-center" style="font-weight:bold;">{{ $r->total_penerima }}</td>
                    <td class="td-center">{{ $r->total_sudah_survei }}</td>
                    <td class="td-center" style="color:#b78100;">{{ $b }}</td>
                    <td class="td-center badge-layak">{{ $r->total_layak }}</td>
                    <td class="td-center badge-tidak">{{ $r->total_tidak_layak }}</td>
                    <td class="td-center" style="font-weight:bold;">{{ $pctSurvei }}%</td>
                    <td class="td-center" style="font-weight:bold;color:#166534;">{{ $pctKesesuaian }}%</td>
                </tr>
            @endforeach
            <tr style="background-color:#e2e8f0;font-weight:bold;">
                <td colspan="3" class="td-cell" style="text-align:right;">TOTAL KESELURUHAN:</td>
                <td class="td-center">{{ $sumTotal }}</td>
                <td class="td-center">{{ $sumSudah }}</td>
                <td class="td-center">{{ $sumBelum }}</td>
                <td class="td-center" style="color:#166534;">{{ $sumLayak }}</td>
                <td class="td-center" style="color:#991b1b;">{{ $sumTidak }}</td>
                <td class="td-center">{{ $sumTotal > 0 ? round(($sumSudah/$sumTotal)*100,1) : 0 }}%</td>
                <td class="td-center" style="color:#166534;">{{ $sumSudah > 0 ? round(($sumLayak/$sumSudah)*100,1) : 0 }}%</td>
            </tr>
        </tbody>
    </table>

    <br /><br />

    <!-- TABEL 2: DAFTAR DETAIL PENERIMA LENGKAP DENGAN FOTO LAPANGAN -->
    <h3 style="color:#002855;margin-bottom:5px;">II. DAFTAR DETAIL CALON PENERIMA &amp; LAMPIRAN FOTO LAPANGAN</h3>
    <table border="1" style="border-collapse:collapse;">
        <thead>
            <tr>
                <th class="th-sub" style="width:35px;">NO</th>
                <th class="th-sub" style="width:160px;">NAMA PENERIMA</th>
                <th class="th-sub" style="width:130px;">NIK</th>
                <th class="th-sub" style="width:130px;">NO KK</th>
                <th class="th-sub" style="width:120px;">KECAMATAN</th>
                <th class="th-sub" style="width:120px;">DESA / KELURAHAN</th>
                <th class="th-sub" style="width:180px;">ALAMAT</th>
                <th class="th-sub" style="width:130px;">STATUS KELAYAKAN</th>
                <th class="th-sub" style="width:150px;">INDIKATOR RTLH</th>
                <th class="th-sub" style="width:120px;height:30px;">FOTO TAMPAK DEPAN</th>
                <th class="th-sub" style="width:120px;">FOTO INTERIOR/DALAM</th>
                <th class="th-sub" style="width:120px;">FOTO DOKUMEN KTP</th>
            </tr>
        </thead>
        <tbody>
            @forelse($detailPenerima as $idx => $p)
                <tr>
                    <td class="td-center">{{ $idx + 1 }}</td>
                    <td class="td-cell" style="font-weight:bold;color:#002855;">{{ $p->nama }}</td>
                    <td class="td-center" style="font-family:monospace;">'{{ $p->no_ktp }}</td>
                    <td class="td-center" style="font-family:monospace;">'{{ $p->no_kk }}</td>
                    <td class="td-cell">{{ $p->kecamatan }}</td>
                    <td class="td-cell">{{ $p->desa_kelurahan }}</td>
                    <td class="td-cell">{{ $p->alamat }}</td>
                    <td class="td-center {{ $p->status_kelayakan === 'Layak Diusulkan' ? 'badge-layak' : ($p->status_kelayakan === 'Tidak Layak Diusulkan' ? 'badge-tidak' : 'badge-belum') }}">
                        {{ $p->status_kelayakan ?: 'Belum Survei' }}
                    </td>
                    <td class="td-cell" style="font-size:8.5pt;">
                        @php
                            $ind = [];
                            if ($p->indikator_atap === 'tidak_ada') $ind[] = 'Atap';
                            if ($p->indikator_dinding === 'tidak_ada') $ind[] = 'Dinding';
                            if ($p->indikator_lantai === 'tidak_ada') $ind[] = 'Lantai';
                            if ($p->indikator_pondasi === 'tidak_ada') $ind[] = 'Pondasi';
                            if ($p->indikator_struktur === 'tidak_ada') $ind[] = 'Struktur';
                            if ($p->indikator_penghasilan === 'ada') $ind[] = 'Penghasilan Low';
                        @endphp
                        {{ !empty($ind) ? implode(', ', $ind) : '-' }}
                    </td>
                    <td class="td-center" style="padding:4px;vertical-align:middle;height:85px;">
                        @if(!empty($p->foto_depan_base64))
                            <img src="{{ $p->foto_depan_base64 }}" width="100" height="75" style="border:1px solid #cbd5e1;object-fit:cover;" />
                        @else
                            <span style="color:#94a3b8;font-size:8pt;">(Tanpa Foto)</span>
                        @endif
                    </td>
                    <td class="td-center" style="padding:4px;vertical-align:middle;height:85px;">
                        @if(!empty($p->foto_dalam_base64))
                            <img src="{{ $p->foto_dalam_base64 }}" width="100" height="75" style="border:1px solid #cbd5e1;object-fit:cover;" />
                        @else
                            <span style="color:#94a3b8;font-size:8pt;">(Tanpa Foto)</span>
                        @endif
                    </td>
                    <td class="td-center" style="padding:4px;vertical-align:middle;height:85px;">
                        @if(!empty($p->foto_ktp_base64))
                            <img src="{{ $p->foto_ktp_base64 }}" width="100" height="75" style="border:1px solid #cbd5e1;object-fit:cover;" />
                        @else
                            <span style="color:#94a3b8;font-size:8pt;">(Tanpa KTP)</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="td-center" style="padding:20px;color:#94a3b8;">Belum ada data detail penerima.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
