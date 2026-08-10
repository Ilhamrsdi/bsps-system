@php
    $kecamatansCoords = [
        'kaliwates' => [-8.1844, 113.6844],
        'sumbersari' => [-8.1801, 113.7230],
        'patrang' => [-8.1438, 113.7125],
        'ajung' => [-8.2305, 113.6711],
        'rambipuji' => [-8.2033, 113.6144],
        'balung' => [-8.2678, 113.5361],
        'ambulu' => [-8.3461, 113.6061],
        'wuluhan' => [-8.3211, 113.5511],
        'puger' => [-8.3789, 113.4756],
        'kencong' => [-8.2811, 113.3761],
        'gumukmas' => [-8.3211, 113.4211],
        'umbulsari' => [-8.2511, 113.4411],
        'semboro' => [-8.2111, 113.4711],
        'jombang' => [-8.2811, 113.3211],
        'silo' => [-8.2311, 113.8911],
        'mayang' => [-8.1911, 113.7911],
        'mumbulsari' => [-8.2511, 113.7611],
        'jenggawah' => [-8.2611, 113.7011],
        'tempurejo' => [-8.3111, 113.7511],
        'pakusari' => [-8.1611, 113.7711],
        'sukowono' => [-8.0711, 113.8011],
        'kalisat' => [-8.1211, 113.8111],
        'ledokombo' => [-8.1111, 113.8711],
        'sumberjambe' => [-8.0311, 113.8411],
        'arjasa' => [-8.1111, 113.7211],
        'jelbuk' => [-8.0511, 113.7111],
        'bangsalsari' => [-8.1811, 113.5311],
        'panti' => [-8.1211, 113.6111],
        'sukorambi' => [-8.1511, 113.6511],
        'tanggul' => [-8.1611, 113.4511],
        'sumberbaru' => [-8.1111, 113.3711],
    ];

    $dm = $bap->dataMingguan;
    $survey = $dm ? $dm->surveys->first() : null;
    $assignedPetugas = $dm ? $dm->petugas : collect();

    // Data Petugas
    if ($assignedPetugas->count() > 0) {
        $petugas1 = $assignedPetugas->get(0)->name;
        $petugas2 = $assignedPetugas->count() > 1 ? $assignedPetugas->get(1)->name : '-';
    } else {
        $petugas1 = '-';
        $petugas2 = '-';
    }

    // Coordinates GPS
    if ($survey && $survey->latitude && $survey->longitude) {
        $lat = (float) $survey->latitude;
        $lng = (float) $survey->longitude;
    } else {
        $lokasiKey = strtolower($dm->lokasi ?? '');
        $coords = $kecamatansCoords[$lokasiKey] ?? [-8.1844, 113.6844];
        $lat = $coords[0];
        $lng = $coords[1];
    }

    // Data Pemohon
    $namaPemohon = ($survey && $survey->nama_pemohon) ? $survey->nama_pemohon : ($dm->nama_pemohon ?? '-');
    $nikPemohon = ($survey && $survey->nik_pemohon) ? $survey->nik_pemohon : ($dm->nik_pemohon ?? '-');
    $alamatPemohon = ($survey && $survey->alamat_pemohon) ? $survey->alamat_pemohon : ($dm->alamat ?? '-');

    // Data Bangunan Gedung
    $jenisBangunan = ($survey && $survey->jenis_bangunan) ? $survey->jenis_bangunan : ($dm->jenis_bangunan ?? 'Rumah Kediaman');
    $fungsiBangunan = ($survey && $survey->fungsi_bangunan) ? $survey->fungsi_bangunan : ($dm->fungsi_bangunan ?? 'Fungsi Hunian');
    $luasBangunan = ($survey && $survey->luas_bangunan) ? $survey->luas_bangunan . ' m²' : ($dm->luas_bangunan ? $dm->luas_bangunan . ' m²' : '-');
    $jumlahLantai = ($survey && $survey->jumlah_lantai) ? $survey->jumlah_lantai . ' Lantai' : ($dm->jumlah_lantai ? $dm->jumlah_lantai . ' Lantai' : '-');
    $tinggiBangunan = ($survey && $survey->tinggi_bangunan) ? $survey->tinggi_bangunan . ' Meter' : ($dm->tinggi_bangunan ? $dm->tinggi_bangunan . ' Meter' : '-');
    $alamatLokasi = ($survey && $survey->alamat_lokasi) ? $survey->alamat_lokasi : ($dm->alamat ?? '-');

    // Checklist Items
    $vAdmin = ($survey && $survey->item_admin) ? $survey->item_admin : 'Sesuai';
    $vFungsi = ($survey && $survey->item_fungsi) ? $survey->item_fungsi : 'Sesuai';
    $vPeruntukan = ($survey && $survey->item_peruntukan) ? $survey->item_peruntukan : 'Sesuai';
    $vTata = ($survey && $survey->item_tata) ? $survey->item_tata : 'Sesuai';
    $vKelaikan = ($survey && $survey->item_kelaikan) ? $survey->item_kelaikan : 'Sesuai';

    // Dates
    $namaKegiatan = $dm->nama_kegiatan ?? '-';
    $kecamatanName = ucwords(str_replace('_', ' ', $dm->lokasi ?? ''));
    $desaKelurahanName = ($survey && $survey->desa_kelurahan) ? $survey->desa_kelurahan : ($dm->desa_kelurahan ?? '-');
    $namaJalan = ($survey && $survey->nama_jalan) ? $survey->nama_jalan : ($dm->nama_jalan ?? '-');
    $tanggalFormatted = $dm->tanggal ? $dm->tanggal->translatedFormat('d F Y') : '-';
    $tanggalDoc = $bap->created_at->translatedFormat('d F Y');
    $hariDoc = $bap->created_at->translatedFormat('l');
    $tahunDoc = $bap->created_at->format('Y');
    $bulanDoc = $bap->created_at->translatedFormat('F');

    $hariIndo = match(strtolower($hariDoc)) {
        'sunday' => 'Minggu', 'monday' => 'Senin', 'tuesday' => 'Selasa', 'wednesday' => 'Rabu',
        'thursday' => 'Kamis', 'friday' => 'Jumat', 'saturday' => 'Sabtu',
        default => $hariDoc
    };
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cetak BAP Resmi - Dinas PUPR Kabupaten Jember</title>

    <!-- Favicon Logo PUPR -->
    <link rel="icon" href="{{ asset('logo.jpg') }}" type="image/jpeg" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Leaflet CSS & JS untuk Google Satellite Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Inter', sans-serif;
            background: #cbd5e1;
            color: #000000;
            padding: 24px 0 40px;
            font-size: 13px;
            line-height: 1.5;
        }

        /* Floating Toolbar (Tombol Cetak & Tutup) */
        .print-toolbar {
            position: fixed;
            top: 16px;
            right: 24px;
            z-index: 9999;
            display: flex;
            gap: 12px;
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(8px);
            padding: 10px 18px;
            border-radius: 50px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .print-btn {
            padding: 8px 20px;
            border-radius: 30px;
            border: none;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-print-action {
            background: #002855;
            color: #fff;
        }
        .btn-print-action:hover {
            background: #1a5276;
        }

        .btn-close-action {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }
        .btn-close-action:hover {
            background: rgba(239, 68, 68, 0.9);
        }

        /* Lembar Kertas Dokumen BAP Ukuran F4 / Folio (215mm x 330mm) */
        .paper-container {
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 32px;
        }

        /* Halaman Portrait F4 (215mm x 330mm) */
        .paper-page {
            background: #ffffff;
            padding: 44px 54px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
            position: relative;
            width: 812px; /* 215mm */
            min-height: 1248px; /* 330mm F4 */
        }

        /* Halaman 3 Landscape F4 (330mm x 215mm) */
        .paper-page.landscape-page {
            width: 1248px; /* 330mm F4 */
            min-height: 812px; /* 215mm F4 */
            padding: 36px 44px;
        }

        /* Kop Surat Resmi Dinas PUPR Jember */
        .bap-kop {
            display: flex;
            align-items: center;
            border-bottom: 4px double #000000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .bap-kop-logo {
            width: 75px;
            height: auto;
            flex-shrink: 0;
            margin-right: 16px;
        }

        .bap-kop-text {
            flex: 1;
            text-align: center;
        }

        .bap-kop-text h4 {
            font-size: 15px;
            font-weight: 800;
            color: #000;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .bap-kop-text h3 {
            font-size: 18px;
            font-weight: 900;
            color: #000;
            margin: 2px 0 4px;
        }

        .bap-kop-text p {
            font-size: 11px;
            margin: 1px 0;
            color: #1e293b;
        }

        /* Form Tables & Layout */
        .doc-title {
            text-align: center;
            font-size: 15px;
            font-weight: 900;
            text-decoration: underline;
            margin-bottom: 18px;
            color: #000;
            text-transform: uppercase;
        }

        .doc-subno {
            text-align: center;
            font-size: 13px;
            font-weight: 600;
            margin-top: -12px;
            margin-bottom: 18px;
        }

        .table-form {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 13px;
        }

        .table-form td {
            padding: 4px 0;
            vertical-align: top;
        }

        .table-form td.label {
            width: 160px;
            font-weight: 700;
        }

        .table-form td.sep {
            width: 15px;
        }

        .table-form td.val {
            font-weight: 600;
        }

        /* List Poin Pemeriksaan */
        .check-list {
            padding-left: 20px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .check-list li {
            margin-bottom: 10px;
            text-align: justify;
        }

        /* SVG Diagram Potongan Jalan */
        .diagram-box {
            text-align: center;
            margin: 20px 0;
            border: 1px solid #cbd5e1;
            padding: 16px;
            border-radius: 6px;
            background: #fafafa;
        }

        .diagram-legend {
            margin-top: 14px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            font-size: 12px;
            font-weight: 700;
            align-items: flex-start;
            padding-left: 20px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .legend-color {
            width: 32px;
            height: 12px;
            border-radius: 2px;
        }

        /* Grid Signatures */
        .sign-grid {
            display: flex;
            justify-content: space-between;
            margin-top: 36px;
            text-align: center;
            font-size: 13px;
        }

        .sign-col {
            width: 280px;
        }

        .sign-col .title {
            margin-bottom: 60px;
        }

        .sign-col .line {
            display: block;
            border-bottom: 1px solid #000;
            width: 100%;
            margin-top: 6px;
        }

        /* Gambar Situasi Box Grid (Page 3 Landscape) */
        .situasi-container {
            border: 2px solid #000;
            margin-top: 10px;
        }

        .situasi-top {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            border-bottom: 2px solid #000;
        }

        .situasi-map-box {
            border-right: 2px solid #000;
            padding: 10px;
            text-align: center;
        }

        .situasi-map-box h4 {
            font-size: 15px;
            font-weight: 900;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        #mapSituasi {
            width: 100%;
            height: 310px;
            border: 1px solid #000;
            z-index: 1;
        }

        .situasi-meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .situasi-meta-table td {
            padding: 6px 10px;
            border-bottom: 1px solid #000;
            vertical-align: top;
        }

        .situasi-meta-table td.lbl {
            font-weight: 700;
            width: 140px;
        }

        .situasi-bottom {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            padding: 16px 20px;
            min-height: 170px;
        }

        @media print {
            @page {
                size: 215mm 330mm; /* Standar Ukuran Kertas F4 / Folio Portrait (Halaman 1 & 2) */
                margin: 8mm 12mm;
            }

            @page landscape-section {
                size: 330mm 215mm; /* Standar Ukuran Kertas F4 / Folio Landscape (Halaman 3) */
                margin: 8mm 12mm;
            }

            body {
                background: #ffffff;
                padding: 0;
            }
            .print-toolbar {
                display: none !important;
            }
            .paper-container {
                gap: 0;
                display: block;
            }
            .paper-page {
                box-shadow: none;
                padding: 24px 36px;
                width: 100%;
                min-height: auto;
                page-break-after: always;
            }
            .paper-page.landscape-page {
                page: landscape-section;
                page-break-before: always;
                page-break-after: avoid;
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Action Toolbar -->
    <div class="print-toolbar no-print">
        <button class="print-btn btn-print-action" onclick="window.print()">
            <i class="fas fa-print"></i> Cetak / Simpan PDF
        </button>
        <button class="print-btn btn-close-action" onclick="window.close()">
            <i class="fas fa-times"></i> Tutup Tab
        </button>
    </div>

    <div class="paper-container">

        <!-- ============================================================
             HALAMAN 1: BERITA ACARA PEMERIKSAAN LAPANGAN (PORTRAIT)
             ============================================================ -->
        <div class="paper-page">
            <!-- Kop Surat Resmi -->
            <div class="bap-kop">
                <img src="{{ asset('logo.jpg') }}" alt="PUPR Logo" class="bap-kop-logo" />
                <div class="bap-kop-text">
                    <h4>PEMERINTAH KABUPATEN JEMBER</h4>
                    <h3>DINAS PEKERJAAN UMUM DAN PENATAAN RUANG</h3>
                    <p>Jalan Brawijaya Nomor 63 Jubung Telp. (0331) 487934 – 426994 Fax 426994</p>
                    <p>Email : dpupr@jemberkab.go.id &bull; Website : dpupr.jemberkab.go.id</p>
                    <p>geoportal-dpupr.jemberkab.go.id</p>
                </div>
            </div>

            <!-- Judul Dokumen -->
            <div class="doc-title">BERITA ACARA PEMERIKSAAN LAPANGAN</div>

            <p style="margin-bottom:12px;">
                Pada Hari ini Saya Petugas Survey Dinas Pekerjaan Umum dan Penataan Ruang, telah melakukan pemeriksaan terhadap:
            </p>

            <table class="table-form">
                <tr>
                    <td class="label">Nama Pemohon</td>
                    <td class="sep">:</td>
                    <td class="val" id="h1Nama">{{ $namaPemohon }}</td>
                </tr>
                <tr>
                    <td class="label">Alamat Pemohon</td>
                    <td class="sep">:</td>
                    <td class="val" id="h1Alamat">{{ $alamatPemohon }}</td>
                </tr>
                <tr>
                    <td class="label">Jenis Bangunan</td>
                    <td class="sep">:</td>
                    <td class="val" id="h1Jenis">{{ $jenisBangunan }}</td>
                </tr>
                <tr>
                    <td class="label">Lantai</td>
                    <td class="sep">:</td>
                    <td class="val" id="h1Lantai">{{ $jumlahLantai }}</td>
                </tr>
                <tr>
                    <td class="label">Letak Bangunan</td>
                    <td class="sep">:</td>
                    <td class="val" id="h1Letak">{{ $alamatLokasi }}</td>
                </tr>
            </table>

            <p style="font-weight:700;margin-bottom:8px;">Didapatkan data-data sebagai berikut :</p>

            <ol class="check-list">
                <li><strong>Proses Pembangunan</strong>: <u>Sudah / Selesai</u> Pembangunan.</li>
                <li><strong>Kesesuaian IMB/PBG</strong>: Bangunan <u>Sesuai</u> dengan pengajuan permohonan Izin Mendirikan Bangunan (IMB).</li>
                <li><strong>Garis Sempadan Tritis</strong>: Bangunan <u>{{ ($survey && $survey->pelanggaran_sempadan > 0) ? 'Melanggar' : 'Tidak melanggar' }}</u> garis sempadan tritis bangunan sebanyak <strong>{{ $survey ? ($survey->pelanggaran_sempadan ?? 0) : 0 }} M</strong>, diukur dari AS jalan <strong>{{ $survey ? ($survey->jarak_as_jalan ?? 6) : 6 }} M</strong>, seharusnya mencapai ukuran <strong>{{ $survey ? ($survey->garis_sempadan_tritis ?? 6) : 6 }} M</strong> diukur dari AS jalan.</li>
                <li><strong>Gambar Potongan Melintang Jalan</strong>: <span id="h1Jalan">{{ $namaJalan }}</span>, Kelurahan/Desa: <span id="h1Desa">{{ $desaKelurahanName }}</span>, Kecamatan: <span id="h1Kec">{{ $kecamatanName }}</span>.</li>
                <li>
                    <strong>Catatan Hasil Pemeriksaan Lapangan</strong>:
                    <div style="border-bottom:1px solid #000;padding:4px 0;margin-top:4px;" id="h1Catatan">
                        {{ $survey && $survey->catatan_survei ? $survey->catatan_survei : 'Pemeriksaan fisik sarana prasarana jalan dan drainase dalam kondisi baik. Pekerjaan direkomendasikan untuk penerbitan Sertifikat Kelaikan Fungsi (SLF) resmi Dinas PUPR Jember.' }}
                    </div>
                </li>
            </ol>

            <!-- Diagram Potongan Melintang Jalan SVG -->
            <div class="diagram-box">
                <svg width="100%" height="110" viewBox="0 0 600 110" xmlns="http://www.w3.org/2000/svg">
                    <!-- Rumah Kiri -->
                    <polygon points="40,55 100,20 160,55" fill="none" stroke="#64748b" stroke-width="1.5"/>
                    <rect x="50" y="55" width="100" height="40" fill="none" stroke="#64748b" stroke-width="1.5"/>

                    <!-- Rumah Kanan -->
                    <polygon points="440,55 500,20 560,55" fill="none" stroke="#64748b" stroke-width="1.5"/>
                    <rect x="450" y="55" width="100" height="40" fill="none" stroke="#64748b" stroke-width="1.5"/>

                    <!-- Jalan AS -->
                    <line x1="160" y1="95" x2="440" y2="95" stroke="#334155" stroke-width="2"/>
                    <line x1="300" y1="20" x2="300" y2="95" stroke="#64748b" stroke-width="1" stroke-dasharray="4"/>

                    <!-- Garis Pagar HR (Hijau) -->
                    <line x1="220" y1="35" x2="220" y2="95" stroke="#16a34a" stroke-width="4"/>
                    <line x1="380" y1="35" x2="380" y2="95" stroke="#16a34a" stroke-width="4"/>

                    <!-- Garis Tritis GR (Kuning) -->
                    <line x1="180" y1="30" x2="180" y2="95" stroke="#eab308" stroke-width="4"/>
                    <line x1="420" y1="30" x2="420" y2="95" stroke="#eab308" stroke-width="4"/>

                    <!-- Text Labels -->
                    <text x="175" y="22" font-size="11" font-weight="bold" fill="#000">GR</text>
                    <text x="215" y="22" font-size="11" font-weight="bold" fill="#000">HR</text>
                    <text x="292" y="15" font-size="11" font-weight="bold" fill="#000">AS</text>
                    <text x="375" y="22" font-size="11" font-weight="bold" fill="#000">HR</text>
                    <text x="415" y="22" font-size="11" font-weight="bold" fill="#000">GR</text>
                </svg>

                <div class="diagram-legend">
                    <div class="legend-item"><div class="legend-color" style="background:#16a34a;"></div> Garis Pagar (HR)</div>
                    <div class="legend-item"><div class="legend-color" style="background:#eab308;"></div> Garis Tritis (GR)</div>
                    <div class="legend-item"><div class="legend-color" style="background:#94a3b8;border:1px solid #000;"></div> Letak Bangunan yang melanggar</div>
                    <div class="legend-item"><div class="legend-color" style="background:#d43f78;"></div> Bangunan yang diijinkan</div>
                </div>
            </div>

            <!-- Signatures Page 1 -->
            <div class="sign-grid">
                <div class="sign-col">
                    <div class="title">Pemohon,</div>
                    <div style="height:60px;"></div>
                    <span class="line"></span>
                    <div id="signPemohonH1" style="font-weight:700;margin-top:4px;">{{ $namaPemohon }}</div>
                </div>
                <div class="sign-col">
                    <div class="title">
                        Jember, <span id="signTglH1">{{ $tanggalDoc }}</span><br>
                        Petugas Survey Lapangan
                    </div>
                    <div style="text-align:left;font-size:12px;margin-top:10px;">
                        1. <span id="signPetugas1H1" style="font-weight:700;">{{ $petugas1 }}</span> <span class="line"></span>
                        <br>
                        2. <span id="signPetugas2H1" style="font-weight:700;">{{ $petugas2 }}</span> <span class="line"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================
             HALAMAN 2: BERITA ACARA VERIFIKASI LAPANGAN KELAIKAN FUNGSI (PORTRAIT)
             ============================================================ -->
        <div class="paper-page">
            <div class="doc-title">BERITA ACARA VERIFIKASI LAPANGAN KELAIKAN FUNGSI BANGUNAN GEDUNG</div>
            <div class="doc-subno" id="h2NoBap">Nomor : {{ $bap->nomor_bap }}</div>
            <div style="text-align:center;font-size:12px;margin-top:-10px;margin-bottom:24px;" id="h2TglDoc">Tanggal: {{ $tanggalDoc }}</div>

            <p style="margin-bottom:14px;text-align:justify;">
                Pada hari ini, {{ $hariIndo }} tanggal {{ $tanggalDoc }} yang bertanda tangan di bawah ini, telah dilaksanakan verifikasi lapangan kelaikan fungsi bangunan gedung pada:
            </p>

            <p style="font-weight:700;margin-bottom:8px;">1. Bangunan Gedung</p>
            <table class="table-form" style="padding-left:16px;">
                <tr>
                    <td class="label">a. Bangunan</td>
                    <td class="sep">:</td>
                    <td class="val">{{ $jenisBangunan }}</td>
                </tr>
                <tr>
                    <td class="label">b. Fungsi Bangunan</td>
                    <td class="sep">:</td>
                    <td class="val">{{ $fungsiBangunan }}</td>
                </tr>
                <tr>
                    <td class="label">c. Luas Bangunan</td>
                    <td class="sep">:</td>
                    <td class="val">{{ $luasBangunan }}</td>
                </tr>
                <tr>
                    <td class="label">d. Jumlah Lantai</td>
                    <td class="sep">:</td>
                    <td class="val">{{ $jumlahLantai }}</td>
                </tr>
                <tr>
                    <td class="label">e. Tinggi Bangunan</td>
                    <td class="sep">:</td>
                    <td class="val">{{ $tinggiBangunan }}</td>
                </tr>
                <tr>
                    <td class="label">f. Lokasi Bangunan</td>
                    <td class="sep">:</td>
                    <td class="val" id="h2Lokasi">{{ $alamatLokasi }}</td>
                </tr>
            </table>

            <p style="font-weight:700;margin:16px 0 8px;">Dengan ini menyatakan bahwa:</p>
            <table class="table-form" style="padding-left:16px;">
                <tr>
                    <td class="label">1. Persyaratan administratif</td>
                    <td class="sep">:</td>
                    <td class="val">
                        @if($vAdmin === 'Sesuai')
                            <strong>Sesuai</strong> / <span style="text-decoration:line-through;color:#94a3b8;">tidak sesuai</span>
                        @else
                            <span style="text-decoration:line-through;color:#94a3b8;">Sesuai</span> / <strong>Tidak Sesuai</strong>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label" colspan="3">2. Persyaratan teknis</td>
                </tr>
                <tr>
                    <td style="padding-left:16px;">a. Fungsi bangunan gedung</td>
                    <td>:</td>
                    <td>
                        @if($vFungsi === 'Sesuai')
                            <strong>Sesuai</strong> / <span style="text-decoration:line-through;color:#94a3b8;">tidak sesuai</span>
                        @else
                            <span style="text-decoration:line-through;color:#94a3b8;">Sesuai</span> / <strong>Tidak Sesuai</strong>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding-left:16px;">b. Peruntukan</td>
                    <td>:</td>
                    <td>
                        @if($vPeruntukan === 'Sesuai')
                            <strong>Sesuai</strong> / <span style="text-decoration:line-through;color:#94a3b8;">tidak sesuai</span>
                        @else
                            <span style="text-decoration:line-through;color:#94a3b8;">Sesuai</span> / <strong>Tidak Sesuai</strong>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding-left:16px;">c. Tata Bangunan</td>
                    <td>:</td>
                    <td>
                        @if($vTata === 'Sesuai')
                            <strong>Sesuai</strong> / <span style="text-decoration:line-through;color:#94a3b8;">tidak sesuai</span>
                        @else
                            <span style="text-decoration:line-through;color:#94a3b8;">Sesuai</span> / <strong>Tidak Sesuai</strong>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding-left:16px;">d. Kelaikan fungsi bangunan</td>
                    <td>:</td>
                    <td>
                        @if($vKelaikan === 'Sesuai')
                            <strong>Sesuai</strong> / <span style="text-decoration:line-through;color:#94a3b8;">tidak sesuai</span>
                        @else
                            <span style="text-decoration:line-through;color:#94a3b8;">Sesuai</span> / <strong>Tidak Sesuai</strong>
                        @endif
                    </td>
                </tr>
            </table>

            <p style="margin:20px 0;text-align:justify;">
                Sesuai dengan kesimpulan berdasarkan analisis terhadap Daftar Simak Pemeriksaan Kelaikan Fungsi Bangunan Gedung terlampir.
            </p>
            <p style="margin-bottom:30px;text-align:justify;">
                Berita Acara ini berlaku sepanjang tidak ada perubahan yang dilakukan pemilik/pengguna yang mengubah sistem dan/atau spesifikasi teknis, atau gangguan penyebab lainnya yang dibuktikan kemudian.
            </p>

            <!-- Signatures Page 2 -->
            <div class="sign-grid">
                <div class="sign-col">
                    <div class="title">PEMOHON</div>
                    <div style="height:70px;"></div>
                    <span class="line"></span>
                    <div id="signPemohonH2" style="font-weight:700;margin-top:4px;">{{ $namaPemohon }}</div>
                </div>
                <div class="sign-col">
                    <div class="title">
                        Jember, <span id="signTglH2">{{ $tanggalDoc }}</span><br>
                        TPT Dinas Pekerjaan Umum dan Penataan Ruang<br>
                        Kabupaten Jember
                    </div>
                    <div style="text-align:left;font-size:12px;margin-top:10px;">
                        1. <span id="signPetugas1H2" style="font-weight:700;">{{ $petugas1 }}</span> <span class="line"></span>
                        <br>
                        2. <span id="signPetugas2H2" style="font-weight:700;">{{ $petugas2 }}</span> <span class="line"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================
             HALAMAN 3: GAMBAR SITUASI & KOORDINAT SPASIAL (HORIZONTAL / LANDSCAPE)
             ============================================================ -->
        <div class="paper-page landscape-page">
            <div class="situasi-container">
                <div class="situasi-top">
                    <!-- Box Kiri: Leaflet Google Satellite Map Live -->
                    <div class="situasi-map-box">
                        <h4>GAMBAR SITUASI</h4>
                        <!-- Live Leaflet Google Satellite Map Container -->
                        <div id="mapSituasi"></div>
                    </div>

                    <!-- Box Kanan: Kop Mini & Tabel Registrasi -->
                    <div>
                        <div style="display:flex;align-items:center;padding:10px;border-bottom:2px solid #000;">
                            <img src="{{ asset('logo.jpg') }}" alt="PUPR Logo" style="width:55px;height:auto;margin-right:12px;" />
                            <div style="font-size:11px;text-align:center;font-weight:800;flex:1;">
                                PEMERINTAH KABUPATEN JEMBER<br>
                                DINAS PEKERJAAN UMUM DAN PENATAAN RUANG<br>
                                <span style="font-weight:400;font-size:9px;">Jalan Brawijaya No. 63 Jubung Telp. (0331) 487934 – 426994 Fax 426994</span><br>
                                <span style="font-weight:400;font-size:9px;">Email : dpupr@jemberkab.go.id Website : dpupr.jemberkab.go.id</span>
                            </div>
                        </div>

                        <table class="situasi-meta-table">
                            <tr>
                                <td class="lbl">NOMOR</td>
                                <td>: {{ $bap->nomor_bap }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">NAMA PEMOHON</td>
                                <td>: <strong id="h3Nama">{{ $namaPemohon }}</strong></td>
                            </tr>
                            <tr>
                                <td class="lbl">LOKASI</td>
                                <td id="h3Lokasi">: {{ $alamatLokasi }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">DESA/KELURAHAN</td>
                                <td>: {{ strtoupper($desaKelurahanName) }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">KECAMATAN</td>
                                <td>: {{ strtoupper($kecamatanName) }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">STATUS HAK TANAH</td>
                                <td>: {{ $survey && $survey->status_hak_tanah ? $survey->status_hak_tanah : ($dm->status_hak_tanah ?? 'Hak Guna Bangunan') }}</td>
                            </tr>
                            <tr>
                                <td class="lbl" style="border-bottom:none;">LUAS TANAH</td>
                                <td style="border-bottom:none;">: {{ $survey && $survey->luas_tanah ? $survey->luas_tanah . ' m²' : ($dm->luas_tanah ? $dm->luas_tanah . ' m²' : '-') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="situasi-bottom">
                    <div>
                        <strong style="font-size:13px;display:block;margin-bottom:8px;text-transform:uppercase;">KETERANGAN :</strong>
                        <p style="font-size:13px;margin-bottom:10px;">Termasuk Jalan : <strong id="h3Jalan">{{ $namaJalan }}</strong></p>
                        <p style="font-size:13px;">Titik Koordinat : <strong id="h3Koordinat" style="text-decoration:underline;">{{ $lat }}, {{ $lng }}</strong></p>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1.2fr;text-align:center;font-size:12px;font-weight:700;">
                        <div>
                            PEMOHON,
                            <div style="height:70px;"></div>
                            <span style="display:block;border-bottom:1px solid #000;width:80%;margin:0 auto;">{{ $namaPemohon }}</span>
                        </div>
                        <div>
                            PETUGAS SURVEY,
                            <div style="text-align:left;margin-top:20px;font-size:11px;padding-left:14px;line-height:2.2;">
                                1. {{ $petugas1 }}<br>
                                2. {{ $petugas2 }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================
             HALAMAN 4: DOKUMENTASI FOTO LAPANGAN (HORIZONTAL / LANDSCAPE)
             ============================================================ -->
        <div class="paper-page landscape-page">
            <div class="situasi-container" style="min-height:580px;">
                <!-- Header Meta Table Grid -->
                <div style="display:grid;grid-template-columns:1.8fr 1.2fr;border-bottom:2px solid #000;">
                    <table class="situasi-meta-table" style="border-right:2px solid #000;">
                        <tr>
                            <td class="lbl">NAMA PEMOHON</td>
                            <td>: <strong id="h4Nama">{{ $namaPemohon }}</strong></td>
                        </tr>
                        <tr>
                            <td class="lbl">BANGUNAN</td>
                            <td>: <strong id="h4Bangunan">{{ $jenisBangunan }}</strong></td>
                        </tr>
                        <tr>
                            <td class="lbl" style="border-bottom:none;">LOKASI</td>
                            <td style="border-bottom:none;" id="h4Lokasi">: {{ $alamatLokasi }}</td>
                        </tr>
                    </table>

                    <div style="display:flex;align-items:center;padding:16px;font-size:14px;font-weight:700;">
                        NO. Register : <span style="margin-left:8px;font-weight:900;letter-spacing:0.5px;">{{ $bap->nomor_bap }}</span>
                    </div>
                </div>

                <!-- Foto Grid Area (Tampak Lapangan) -->
                <div style="padding:20px;display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                    <div style="border:1px solid #000;padding:10px;text-align:center;background:#fff;">
                        <img src="https://images.unsplash.com/photo-1590069261209-f8e9b8642343?auto=format&fit=crop&w=600&q=80" alt="Dokumentasi Bangunan Depan" style="width:100%;height:320px;object-fit:cover;border:1px solid #64748b;" />
                        <span style="font-size:12px;font-weight:800;margin-top:8px;display:block;text-transform:uppercase;">FOTO TAMPAK DEPAN BANGUNAN & LAPANGAN</span>
                    </div>
                    <div style="border:1px solid #000;padding:10px;text-align:center;background:#fff;">
                        <img src="https://images.unsplash.com/photo-1541888946425-d0fbb186a5b7?auto=format&fit=crop&w=600&q=80" alt="Dokumentasi Akses Jalan" style="width:100%;height:320px;object-fit:cover;border:1px solid #64748b;" />
                        <span style="font-size:12px;font-weight:800;margin-top:8px;display:block;text-transform:uppercase;">FOTO AKSES JALAN & DRAINASE LOKASI</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Script Dynamic Auto Populate, Leaflet Satellite Map & Print -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Live Google Satellite Map on Page 3
            const lat = {{ $lat }};
            const lng = {{ $lng }};
            const mapSituasi = L.map('mapSituasi', {
                center: [lat, lng],
                zoom: 16,
                zoomControl: false,
                attributionControl: false
            });

            // Authentic Google Maps Satellite Hybrid Tile Layer (lyrs=y)
            L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                maxZoom: 20
            }).addTo(mapSituasi);

            // Custom Red Pin Marker matching screenshot
            const redPinIcon = L.divIcon({
                className: 'custom-red-pin-bap',
                html: `<div style="background:#dc2626;width:22px;height:22px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:2px solid #fff;box-shadow:0 3px 8px rgba(0,0,0,0.6);"></div>`,
                iconSize: [22, 22],
                iconAnchor: [11, 11]
            });
            L.marker([lat, lng], { icon: redPinIcon }).addTo(mapSituasi);

            // Invalidate map size to render perfectly
            setTimeout(function() {
                mapSituasi.invalidateSize();
            }, 300);
        });
    </script>
</body>
</html>
