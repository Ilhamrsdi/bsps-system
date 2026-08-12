<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekapitulasi Hasil Verval BSPS per Desa &amp; Kecamatan</title>

    <link rel="icon" href="{{ asset('logo.jpg') }}" type="image/jpeg" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <style>
        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        body {
            font-family: 'Inter', sans-serif;
            font-size: 11px;
            color: #0f172a;
            background: #fff;
            margin: 0;
            padding: 15px;
        }

        /* Kop Surat Resmi Dinas PUPR Jember */
        .kop-surat {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            border-bottom: 3px double #002855;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .kop-logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .kop-text {
            text-align: center;
        }

        .kop-text h3 {
            font-size: 13px;
            font-weight: 700;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kop-text h2 {
            font-size: 17px;
            font-weight: 900;
            color: #002855;
            margin: 2px 0 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kop-text p {
            font-size: 10.5px;
            margin: 0;
            color: #475569;
        }

        /* Title Document */
        .doc-title {
            text-align: center;
            margin-bottom: 16px;
        }

        .doc-title h1 {
            font-size: 15px;
            font-weight: 800;
            color: #002855;
            text-transform: uppercase;
            margin: 0 0 4px;
            letter-spacing: 0.5px;
        }

        .doc-title p {
            font-size: 10.5px;
            color: #64748b;
            margin: 0;
        }

        /* Summary Stats Cards Print */
        .summary-print-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-bottom: 16px;
        }

        .summary-print-card {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 8px 12px;
            background: #f8fafc;
            text-align: center;
        }

        .summary-print-card .val {
            font-size: 15px;
            font-weight: 800;
            color: #002855;
        }

        .summary-print-card .lbl {
            font-size: 9.5px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
            margin-top: 2px;
        }

        /* Table Cetak */
        table.table-cetak {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
            margin-bottom: 24px;
        }

        table.table-cetak th {
            background: #002855;
            color: #ffffff;
            padding: 8px 6px;
            text-align: center;
            font-weight: 700;
            font-size: 10px;
            text-transform: uppercase;
            border: 1px solid #002855;
        }

        table.table-cetak td {
            padding: 7px 6px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }

        table.table-cetak tr:nth-child(even) {
            background: #f8fafc;
        }

        /* Signature Tanda Tangan */
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .signature-box {
            text-align: center;
            width: 250px;
        }

        .signature-box .role {
            font-size: 10.5px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 55px;
        }

        .signature-box .name {
            font-size: 11.5px;
            font-weight: 800;
            color: #002855;
            text-decoration: underline;
        }

        .signature-box .nip {
            font-size: 10px;
            color: #64748b;
        }

        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }

        .no-print-bar {
            background: #001737;
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <!-- Non-Print Top Bar Controls -->
    <div class="no-print-bar no-print">
        <div>
            <strong style="font-size:14px;"><i class="fas fa-print" style="margin-right:8px;color:#FFB800;"></i>Cetak Laporan Rekapitulasi BSPS Verval</strong>
            <div style="font-size:12px;opacity:0.8;margin-top:2px;">Gunakan orientasi Lanskap (Landscape) saat mencetak ke kertas A4 / F4.</div>
        </div>
        <div style="display:flex;gap:10px;">
            <button onclick="window.history.back()" style="padding:8px 16px;border-radius:6px;border:1px solid rgba(255,255,255,0.3);background:transparent;color:#fff;font-weight:600;cursor:pointer;">
                <i class="fas fa-arrow-left"></i> Kembali
            </button>
            <button onclick="window.print()" style="padding:8px 20px;border-radius:6px;border:none;background:#FFB800;color:#001737;font-weight:800;cursor:pointer;">
                <i class="fas fa-print"></i> Cetak Sekarang
            </button>
        </div>
    </div>

    <!-- Kop Surat Program BSPS -->
    <div class="kop-surat">
        <img src="{{ asset('logo.jpg') }}" alt="Logo BSPS" class="kop-logo">
        <div class="kop-text">
            <h3>Pemerintah Kabupaten Jember</h3>
            <h2>Program Bantuan Stimulan Perumahan Swadaya (BSPS)</h2>
            <p>Posko Tenaga Fasilitator Lapangan (TFL), Kabupaten Jember, Jawa Timur | Email: verval.bsps@jemberkab.go.id</p>
        </div>
    </div>

    <!-- Judul Dokumen -->
    <div class="doc-title">
        <h1>Laporan Rekapitulasi Hasil Verifikasi &amp; Validasi (Sesuai / Tidak Sesuai) per Desa &amp; Kecamatan</h1>
        <p>Dicetak Pada: {{ date('d F Y - H:i') }} WIB &bull; Sistem Informasi BSPS Verval Kabupaten Jember</p>
    </div>

    <!-- Ringkasan Statistik -->
    <div class="summary-print-grid">
        <div class="summary-print-card">
            <div class="val">{{ number_format($stats['total']) }}</div>
            <div class="lbl">Total Penerima</div>
        </div>
        <div class="summary-print-card">
            <div class="val" style="color:#002855;">{{ number_format($stats['sudah']) }}</div>
            <div class="lbl">Sudah Survei</div>
        </div>
        <div class="summary-print-card">
            <div class="val" style="color:#d69e00;">{{ number_format($stats['belum']) }}</div>
            <div class="lbl">Belum Survei</div>
        </div>
        <div class="summary-print-card">
            <div class="val" style="color:#27ae60;">{{ number_format($stats['layak']) }}</div>
            <div class="lbl">Hasil Sesuai (Layak)</div>
        </div>
        <div class="summary-print-card">
            <div class="val" style="color:#e74c3c;">{{ number_format($stats['tidak_layak']) }}</div>
            <div class="lbl">Hasil Tidak Sesuai</div>
        </div>
    </div>

    <!-- Tabel Data Rekapitulasi -->
    <table class="table-cetak">
        <thead>
            <tr>
                <th style="width:30px;">No</th>
                <th style="text-align:left;">Kecamatan</th>
                <th style="text-align:left;">Desa / Kelurahan</th>
                <th>Target</th>
                <th>Sudah Survei</th>
                <th>Belum Survei</th>
                <th>Hasil Sesuai (Layak)</th>
                <th>Tidak Sesuai</th>
                <th>% Capaian</th>
                <th>Atap RTLH</th>
                <th>Dinding RTLH</th>
                <th>Lantai RTLH</th>
                <th>Pondasi RTLH</th>
            </tr>
        </thead>
        <tbody>
            @php
                $sumTotal = 0; $sumSudah = 0; $sumBelum = 0; $sumLayak = 0; $sumTidakLayak = 0;
            @endphp
            @forelse($rekapDesaKecamatan as $index => $row)
                @php
                    $belumSurvei = max(0, $row->total_penerima - $row->total_sudah_survei);
                    $pct = $row->total_sudah_survei > 0 ? round(($row->total_layak / $row->total_sudah_survei) * 100, 1) : 0;
                    $sumTotal += $row->total_penerima;
                    $sumSudah += $row->total_sudah_survei;
                    $sumBelum += $belumSurvei;
                    $sumLayak += $row->total_layak;
                    $sumTidakLayak += $row->total_tidak_layak;
                @endphp
                <tr>
                    <td style="text-align:center;">{{ $index + 1 }}</td>
                    <td><strong>{{ $row->kecamatan }}</strong></td>
                    <td>{{ $row->desa_kelurahan }}</td>
                    <td style="text-align:center;font-weight:700;">{{ number_format($row->total_penerima) }}</td>
                    <td style="text-align:center;">{{ number_format($row->total_sudah_survei) }}</td>
                    <td style="text-align:center;">{{ number_format($belumSurvei) }}</td>
                    <td style="text-align:center;font-weight:700;color:#27ae60;">{{ number_format($row->total_layak) }}</td>
                    <td style="text-align:center;font-weight:700;color:#e74c3c;">{{ number_format($row->total_tidak_layak) }}</td>
                    <td style="text-align:center;font-weight:700;">{{ $pct }}%</td>
                    <td style="text-align:center;">{{ number_format($row->atap_rtlh) }}</td>
                    <td style="text-align:center;">{{ number_format($row->dinding_rtlh) }}</td>
                    <td style="text-align:center;">{{ number_format($row->lantai_rtlh) }}</td>
                    <td style="text-align:center;">{{ number_format($row->pondasi_rtlh) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" style="text-align:center;padding:20px;color:#64748b;">Belum ada data rekapitulasi.</td>
                </tr>
            @endforelse
        </tbody>
        @if($rekapDesaKecamatan->count() > 0)
        <tfoot style="background:#f8fafc;font-weight:800;border-top:2px solid #002855;">
            <tr>
                <td colspan="3" style="text-align:right;padding:8px;">TOTAL KESELURUHAN:</td>
                <td style="text-align:center;">{{ number_format($sumTotal) }}</td>
                <td style="text-align:center;">{{ number_format($sumSudah) }}</td>
                <td style="text-align:center;">{{ number_format($sumBelum) }}</td>
                <td style="text-align:center;color:#27ae60;">{{ number_format($sumLayak) }}</td>
                <td style="text-align:center;color:#e74c3c;">{{ number_format($sumTidakLayak) }}</td>
                <td style="text-align:center;">{{ $sumSudah > 0 ? round(($sumLayak / $sumSudah) * 100, 1) : 0 }}%</td>
                <td colspan="4"></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- Lembar Tanda Tangan -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="role">Mengetahui,<br>Pejabat Pembuat Komitmen (PPK)</div>
            <div class="name">Ir. H. Hendro Supeno, M.T.</div>
            <div class="nip">NIP. 19750812 200212 1 003</div>
        </div>
        <div class="signature-box">
            <div class="role">Jember, {{ date('d F Y') }}<br>Koordinator Fasilitator BSPS Jember</div>
            <div class="name">Dr. Ir. Eko Yuniarto, S.T., M.Si.</div>
            <div class="nip">NIP. 19700415 199603 1 002</div>
        </div>
    </div>

</body>
</html>
