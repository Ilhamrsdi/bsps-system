<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Resmi Hasil Verifikasi BSPS Desa {{ $desa }} - Kec. {{ $kecamatan }}</title>
    <style>
        /* Ukuran Kertas Resmi F4 (Folio) 215mm x 330mm */
        @page {
            size: 215mm 330mm portrait;
            margin: 15mm 18mm 15mm 18mm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10.5pt;
            line-height: 1.25;
            color: #000000;
            margin: 0;
            padding: 0;
        }

        /* Kop Surat Resmi Kedinasan Pemkab Jember */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border: none !important;
            margin-bottom: 2px;
        }

        .kop-table td {
            vertical-align: middle;
            border: none !important;
            padding: 0 !important;
        }

        .kop-logo {
            width: 65px;
            height: 65px;
            display: block;
        }

        .kop-text-center {
            text-align: center;
            padding: 0 10px !important;
        }

        .kop-text-center h3 {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            font-weight: bold;
            margin: 0;
            color: #000000;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kop-text-center h2 {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13.5pt;
            font-weight: 900;
            color: #000000;
            margin: 2px 0 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kop-text-center p {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            color: #111827;
            margin: 0;
        }

        /* Garis Ganda Standar Surat Resmi Dinas */
        .kop-double-line {
            border-top: 2px solid #000000;
            border-bottom: 0.75px solid #000000;
            height: 2px;
            margin-top: 6px;
            margin-bottom: 14px;
        }

        /* Judul Dokumen Laporan */
        .doc-header {
            text-align: center;
            margin-bottom: 14px;
        }

        .doc-header h1 {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 4px 0;
            letter-spacing: 0.3px;
        }

        .doc-header h2 {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 6px 0;
        }

        .doc-header .doc-sub {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #000000;
            padding: 3px 0;
            border-top: 1px solid #000000;
            border-bottom: 1px solid #000000;
            display: block;
            margin-top: 4px;
        }

        /* Section Heading */
        .section-title {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 12px 0 6px 0;
            color: #000000;
        }

        /* Tabel Standar Resmi Kedinasan (Formal Office Grid) */
        table.formal-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 14px;
        }

        table.formal-table th {
            background-color: #f1f5f9;
            color: #000000;
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bold;
            font-size: 8.5pt;
            text-transform: uppercase;
            padding: 6px 5px;
            border: 1px solid #000000;
            text-align: center;
        }

        table.formal-table td {
            padding: 5px 5px;
            border: 1px solid #000000;
            vertical-align: middle;
            color: #000000;
        }

        table.formal-table tr.bg-alt {
            background-color: #fafafa;
        }

        table.formal-table tfoot td {
            font-weight: bold;
            background-color: #f1f5f9;
            border: 1px solid #000000;
        }

        /* Formal Text Helpers */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }

        /* Tanda Tangan Resmi Kedinasan (2 Kolom: Kades & Petugas) */
        .ttd-container {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            page-break-inside: avoid;
            border: none !important;
        }

        .ttd-container td {
            border: none !important;
            padding: 0;
            vertical-align: top;
            font-size: 9.5pt;
            text-align: center;
        }

        .ttd-space {
            height: 60px;
        }

        .ttd-name {
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bold;
            text-decoration: underline;
            color: #000000;
            font-size: 10pt;
        }

        .ttd-role {
            font-size: 9pt;
            color: #000000;
            margin-top: 2px;
        }
    </style>
</head>
<body>

    <!-- Kop Surat Resmi -->
    <table class="kop-table">
        <tr>
            <td style="width: 70px; text-align: left; vertical-align: middle;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" class="kop-logo" alt="Logo Pemkab Jember" />
                @endif
            </td>
            <td class="kop-text-center">
                <h3>PEMERINTAH KABUPATEN JEMBER</h3>
                <h2>DINAS PERUMAHAN RAKYAT, KAWASAN PERMUKIMAN DAN CIPTA KARYA</h2>
                <p>Jl. Hos Cokroaminoto No. 44 Jember, Jawa Timur | Telp. (0331) 487533 | Website: jemberkab.go.id</p>
            </td>
            <td style="width: 70px;"></td>
        </tr>
    </table>

    <!-- Garis Ganda Resmi Kedinasan -->
    <div class="kop-double-line"></div>

    <!-- Judul Dokumen Resmi -->
    <div class="doc-header">
        <h1>LAPORAN HASIL VERIFIKASI DAN VALIDASI DATA LAPANGAN</h1>
        <h2>PROGRAM BANTUAN STIMULAN PERUMAHAN SWADAYA (BSPS)</h2>
        <div class="doc-sub">
            WILAYAH: DESA/KELURAHAN {{ strtoupper($desa ?: 'SEMUA DESA') }}, KECAMATAN {{ strtoupper($kecamatan) }} &bull; TAHUN ANGGARAN {{ date('Y') }}
        </div>
    </div>

    <!-- 1. TABEL REKAPITULASI HASIL VERIFIKASI (FORMAL EKSEKUTIF) -->
    <div class="section-title">I. REKAPITULASI HASIL VERIFIKASI &amp; KELAYAKAN CALON PENERIMA</div>
    <table class="formal-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="text-align: left;">Uraian Hasil Verifikasi Lapangan</th>
                <th style="width: 95px;">Jumlah (KK)</th>
                <th style="width: 95px;">Persentase (%)</th>
                <th style="width: 175px; text-align: left;">Keterangan Kelayakan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>Total Usulan Calon Penerima Terdaftar</td>
                <td class="text-center text-bold">{{ number_format($stats['total']) }}</td>
                <td class="text-center">100,0 %</td>
                <td>Total target verifikasi desa</td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>Sudah Selesai Disurvei Lapangan</td>
                <td class="text-center text-bold">{{ number_format($stats['sudah']) }}</td>
                <td class="text-center">{{ number_format($stats['persen_survei'], 1, ',', '.') }} %</td>
                <td>Survei fisik &amp; berkas lengkap</td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td>Belum Selesai Disurvei Lapangan</td>
                <td class="text-center text-bold">{{ number_format($stats['belum']) }}</td>
                <td class="text-center">{{ number_format(max(0, 100 - $stats['persen_survei']), 1, ',', '.') }} %</td>
                <td>Dalam proses penjadwalan</td>
            </tr>
            <tr class="bg-alt">
                <td class="text-center">4</td>
                <td><strong>Hasil Verifikasi: MEMENUHI SYARAT (LAYAK)</strong></td>
                <td class="text-center text-bold">{{ number_format($stats['layak']) }}</td>
                <td class="text-center text-bold">{{ number_format($stats['persen_layak'], 1, ',', '.') }} %</td>
                <td><strong>Diusulkan Menerima Bantuan</strong></td>
            </tr>
            <tr class="bg-alt">
                <td class="text-center">5</td>
                <td><strong>Hasil Verifikasi: TIDAK MEMENUHI SYARAT</strong></td>
                <td class="text-center text-bold">{{ number_format($stats['tidak_layak']) }}</td>
                <td class="text-center text-bold">{{ number_format($stats['persen_tidak_layak'], 1, ',', '.') }} %</td>
                <td>Tidak memenuhi kriteria RTLH</td>
            </tr>
        </tbody>
    </table>

    <!-- 2. TABEL REKAPITULASI KERUSAKAN 6 INDIKATOR RTLH -->
    <div class="section-title">II. REKAPITULASI CAPAIAN INDIKATOR KERUSAKAN FISIK RUMAH</div>
    <table class="formal-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="text-align: left;">Indikator Keselamatan &amp; Kelayakan Fisik Bangunan</th>
                <th style="width: 95px;">Jumlah (KK)</th>
                <th style="width: 95px;">Persentase (%)</th>
                <th style="width: 175px; text-align: left;">Kondisi / Standar Teknis</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>Kerusakan Konstruksi Atap</td>
                <td class="text-center text-bold">{{ number_format($stats['atap']) }}</td>
                <td class="text-center">{{ $stats['sudah'] > 0 ? number_format(($stats['atap'] / $stats['sudah']) * 100, 1, ',', '.') : '0,0' }} %</td>
                <td>Rangka/penutup atap lapuk/rusak</td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>Kerusakan Konstruksi Dinding</td>
                <td class="text-center text-bold">{{ number_format($stats['dinding']) }}</td>
                <td class="text-center">{{ $stats['sudah'] > 0 ? number_format(($stats['dinding'] / $stats['sudah']) * 100, 1, ',', '.') : '0,0' }} %</td>
                <td>Dinding non-permanen/rusak</td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td>Lantai Belum Kedap Air</td>
                <td class="text-center text-bold">{{ number_format($stats['lantai']) }}</td>
                <td class="text-center">{{ $stats['sudah'] > 0 ? number_format(($stats['lantai'] / $stats['sudah']) * 100, 1, ',', '.') : '0,0' }} %</td>
                <td>Lantai tanah / semen rusak parah</td>
            </tr>
            <tr>
                <td class="text-center">4</td>
                <td>Kerusakan Pondasi Bangunan</td>
                <td class="text-center text-bold">{{ number_format($stats['pondasi']) }}</td>
                <td class="text-center">{{ $stats['sudah'] > 0 ? number_format(($stats['pondasi'] / $stats['sudah']) * 100, 1, ',', '.') : '0,0' }} %</td>
                <td>Pondasi retak / belum berpondasi</td>
            </tr>
            <tr>
                <td class="text-center">5</td>
                <td>Kerusakan Struktur / Kolom Balok</td>
                <td class="text-center text-bold">{{ number_format($stats['struktur']) }}</td>
                <td class="text-center">{{ $stats['sudah'] > 0 ? number_format(($stats['struktur'] / $stats['sudah']) * 100, 1, ',', '.') : '0,0' }} %</td>
                <td>Struktur tidak kokoh/miring</td>
            </tr>
            <tr>
                <td class="text-center">6</td>
                <td>Tingkat Penghasilan Di Bawah UMK</td>
                <td class="text-center text-bold">{{ number_format($stats['penghasilan']) }}</td>
                <td class="text-center">{{ $stats['sudah'] > 0 ? number_format(($stats['penghasilan'] / $stats['sudah']) * 100, 1, ',', '.') : '0,0' }} %</td>
                <td>Masyarakat Berpenghasilan Rendah</td>
            </tr>
        </tbody>
    </table>

    <!-- 3. TABEL DAFTAR RINCIAN PENERIMA -->
    <div class="section-title">III. DAFTAR NOMINATIF CALON PENERIMA BANTUAN SWADAYA</div>
    <table class="formal-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="text-align: left; width: 125px;">Nama Calon Penerima</th>
                <th style="text-align: left; width: 135px;">NIK / No. KK</th>
                <th style="text-align: left;">Alamat / Lingkungan</th>
                <th style="width: 70px; text-align: left;">Indikator Rusak</th>
                <th style="width: 70px;">Status Hasil</th>
            </tr>
        </thead>
        <tbody>
            @forelse($penerimaList as $index => $p)
                <tr class="{{ $index % 2 == 1 ? 'bg-alt' : '' }}">
                    <td class="text-center" style="font-weight: bold;">{{ $index + 1 }}</td>
                    <td>
                        <div style="font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 9pt; color: #000000; text-transform: uppercase;">
                            {{ $p->nama }}
                        </div>
                    </td>
                    <td>
                        {{-- NIK: Bold, Jet-Black, Tebal & Jelas --}}
                        <div style="font-family: Arial, Helvetica, sans-serif; font-size: 9.5pt; font-weight: 900; color: #000000; letter-spacing: 0.3px; line-height: 1.1;">
                            {{ $p->no_ktp ?: '-' }}
                        </div>
                        {{-- KK: Bold, Jet-Black & Sangat Jelas --}}
                        <div style="font-family: Arial, Helvetica, sans-serif; font-size: 8.5pt; font-weight: bold; color: #000000; margin-top: 3px; line-height: 1.1;">
                            <span style="font-weight: 900;">KK:</span> {{ $p->no_kk ?: '-' }}
                        </div>
                    </td>
                    <td>
                        <div style="font-size: 8.5pt; color: #000000;">{{ $p->alamat ?: '-' }}</div>
                        <div style="font-size: 7.5pt; color: #374151; margin-top: 1px;">Ds. {{ $p->desa_kelurahan }}, Kec. {{ $p->kecamatan }}</div>
                    </td>
                    <td style="font-size: 8pt; color: #000000;">
                        @php
                            $inds = [];
                            if ($p->indikator_atap === 'tidak_ada') $inds[] = 'Atap';
                            if ($p->indikator_dinding === 'tidak_ada') $inds[] = 'Dinding';
                            if ($p->indikator_lantai === 'tidak_ada') $inds[] = 'Lantai';
                            if ($p->indikator_pondasi === 'tidak_ada') $inds[] = 'Pondasi';
                            if ($p->indikator_struktur === 'tidak_ada') $inds[] = 'Struktur';
                            if ($p->indikator_penghasilan === 'ada') $inds[] = 'MBR';
                        @endphp
                        {{ count($inds) > 0 ? implode(', ', $inds) : '-' }}
                    </td>
                    <td class="text-center" style="font-size: 8pt;">
                        @if($p->status_kelayakan === 'Layak Diusulkan')
                            <strong style="color: #000000;">LAYAK</strong>
                        @elseif($p->status_kelayakan === 'Tidak Layak Diusulkan')
                            <span style="color: #000000;">TIDAK LAYAK</span>
                        @else
                            <span style="color: #374151;">BELUM SURVEI</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; color: #6b7280;">
                        Tidak ada data calon penerima di wilayah ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Lembar Pengesahan Resmi (2 Kolom: Kepala Desa & Tim Verifikator) -->
    <table class="ttd-container">
        <tr>
            <td style="width: 48%;">
                Mengetahui,<br>
                <strong>{{ $kades ? strtoupper($kades->jabatan) : 'KEPALA DESA / LURAH' }} {{ strtoupper($desa ?: $kecamatan) }}</strong>
                <div class="ttd-space"></div>
                <div class="ttd-name">
                    {{ $kades ? strtoupper($kades->nama) : '( .................................................... )' }}
                </div>
                <div class="ttd-role">
                    @if($kades && !empty($kades->nomor_telepon))
                        Kontak: {{ $kades->nomor_telepon }}
                    @else
                        Kepala Desa / Pejabat Berwenang
                    @endif
                </div>
            </td>
            <td style="width: 4%;"></td>
            <td style="width: 48%;">
                Jember, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                <strong>TIM VERIFIKATOR LAPANGAN BSPS</strong>
                <div class="ttd-space"></div>
                <div class="ttd-name">
                    {{ $petugas ? strtoupper($petugas->name) : '( .................................................... )' }}
                </div>
                <div class="ttd-role">
                    @if($petugas && !empty($petugas->nip))
                        NIP. {{ $petugas->nip }}
                    @else
                        Petugas Verifikasi &amp; Validasi Lapangan
                    @endif
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
