<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekapitulasi Laporan Pekerjaan PUPR Jember</title>

    <link rel="icon" href="{{ asset('logo.jpg') }}" type="image/jpeg" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }

        body {
            font-family: 'Inter', sans-serif;
            font-size: 12px;
            color: #0f172a;
            background: #fff;
            margin: 0;
            padding: 20px;
        }

        /* Kop Surat Resmi Dinas PUPR Jember */
        .kop-surat {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            border-bottom: 3px double #002855;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }

        .kop-logo {
            width: 75px;
            height: 75px;
            object-fit: contain;
        }

        .kop-text {
            text-align: center;
        }

        .kop-text h3 {
            font-size: 14px;
            font-weight: 700;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kop-text h2 {
            font-size: 18px;
            font-weight: 900;
            color: #002855;
            margin: 2px 0 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kop-text p {
            font-size: 11px;
            margin: 0;
            color: #475569;
        }

        /* Title Document */
        .doc-title {
            text-align: center;
            margin-bottom: 20px;
        }

        .doc-title h1 {
            font-size: 16px;
            font-weight: 800;
            color: #002855;
            text-transform: uppercase;
            margin: 0 0 4px;
            letter-spacing: 0.5px;
        }

        .doc-title p {
            font-size: 11px;
            color: #64748b;
            margin: 0;
        }

        /* Summary Stats Cards Print */
        .summary-print-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .summary-print-card {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 10px 14px;
            background: #f8fafc;
        }

        .summary-print-card .val {
            font-size: 16px;
            font-weight: 800;
            color: #002855;
        }

        .summary-print-card .lbl {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
        }

        /* Table Cetak */
        table.table-cetak {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 30px;
        }

        table.table-cetak th {
            background: #002855;
            color: #ffffff;
            padding: 10px 8px;
            text-align: left;
            font-weight: 700;
            font-size: 10.5px;
            text-transform: uppercase;
            border: 1px solid #002855;
        }

        table.table-cetak td {
            padding: 9px 8px;
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
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-box {
            text-align: center;
            width: 250px;
        }

        .signature-box .role {
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 60px;
        }

        .signature-box .name {
            font-size: 12px;
            font-weight: 800;
            color: #002855;
            text-decoration: underline;
        }

        .signature-box .nip {
            font-size: 11px;
            color: #64748b;
        }

        /* Action bar for screen view only */
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
            margin-bottom: 24px;
        }
    </style>
</head>
<body>

    <!-- Non-Print Top Bar Controls -->
    <div class="no-print-bar no-print">
        <div>
            <strong style="font-size:14px;"><i class="fas fa-print" style="margin-right:8px;color:#FFB800;"></i>Pratinjau Cetak Rekapitulasi Laporan Dinas PUPR Jember</strong>
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

    <!-- Kop Surat Dinas PUPR Jember -->
    <div class="kop-surat">
        <img src="{{ asset('logo.jpg') }}" alt="Logo PUPR" class="kop-logo">
        <div class="kop-text">
            <h3>Pemerintah Kabupaten Jember</h3>
            <h2>Dinas Pekerjaan Umum dan Penataan Ruang</h2>
            <p>Jl. Ahmad Yani No. 80, Kabupaten Jember, Jawa Timur | Email: pupr@jemberkab.go.id</p>
        </div>
    </div>

    <!-- Judul Dokumen -->
    <div class="doc-title">
        <h1>Rekapitulasi Laporan Hasil Survei &amp; Pekerjaan Lapangan</h1>
        <p>Dicetak Pada: {{ date('d F Y - H:i') }} WIB &bull; Sistem Monitoring PUPR Kabupaten Jember</p>
    </div>

    <!-- Ringkasan Statistik -->
    <div class="summary-print-grid">
        <div class="summary-print-card">
            <div class="val">{{ $stats['total'] }}</div>
            <div class="lbl">Total Laporan Kegiatan</div>
        </div>
        <div class="summary-print-card">
            <div class="val" style="color:#27ae60;">{{ $stats['terbit'] }}</div>
            <div class="lbl">BAP Terbit / Resmi</div>
        </div>
        <div class="summary-print-card">
            <div class="val" style="color:#d69e00;">{{ $stats['draft'] }}</div>
            <div class="lbl">BAP Dalam Draft</div>
        </div>
        <div class="summary-print-card">
            <div class="val" style="color:#e74c3c;">{{ $stats['belum'] }}</div>
            <div class="lbl">Belum Memiliki BAP</div>
        </div>
    </div>

    <!-- Tabel Data Rekapitulasi -->
    <table class="table-cetak">
        <thead>
            <tr>
                <th style="width:30px;text-align:center;">No</th>
                <th>Nama Kegiatan Lapangan</th>
                <th>Kecamatan &amp; Lokasi</th>
                <th>Nilai Kontrak</th>
                <th>Pelaksana / Kontraktor</th>
                <th style="text-align:center;">Jumlah Survei</th>
                <th>Status BAP</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kegiatans as $index => $item)
                <tr>
                    <td style="text-align:center;">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->nama_kegiatan }}</strong>
                        <div style="font-size:10px;color:#64748b;">{{ $item->alamat ?: '-' }}</div>
                    </td>
                    <td>Kec. {{ ucwords(str_replace('_', ' ', $item->lokasi)) }}</td>
                    <td>
                        @php
                            $valKontrakCetak = is_numeric($item->nilai_kontrak) ? (float)$item->nilai_kontrak : (float)preg_replace('/[^0-9.]/', '', (string)$item->nilai_kontrak);
                        @endphp
                        {{ $valKontrakCetak > 0 ? 'Rp ' . number_format($valKontrakCetak, 0, ',', '.') : ($item->nilai_kontrak ?: '-') }}
                    </td>
                    <td>{{ $item->kontraktor ?: '-' }}</td>
                    <td style="text-align:center;">{{ $item->surveys->count() }} Kali</td>
                    <td>
                        @if($item->bap && $item->bap->status === 'terbit')
                            <strong style="color:#27ae60;">BAP Terbit</strong> ({{ $item->bap->nomor_bap }})
                        @elseif($item->bap && $item->bap->status === 'draft')
                            <span style="color:#d69e00;">Draft BAP</span>
                        @else
                            <span style="color:#e74c3c;">Belum BAP</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:20px;color:#64748b;">Belum ada data kegiatan laporan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Lembar Tanda Tangan -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="role">Mengetahui,<br>Pejabat Pembuat Komitmen (PPK)</div>
            <div class="name">Ir. H. Hendro Supeno, M.T.</div>
            <div class="nip">NIP. 19750812 200212 1 003</div>
        </div>
        <div class="signature-box">
            <div class="role">Jember, {{ date('d F Y') }}<br>Kepala Dinas PUPR Jember</div>
            <div class="name">Dr. Ir. Eko Yuniarto, S.T., M.Si.</div>
            <div class="nip">NIP. 19700415 199603 1 002</div>
        </div>
    </div>

</body>
</html>
