<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lampiran Foto Calon Penerima BSPS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @page {
            size: A4;
            margin: 20mm 20mm 20mm 20mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11.5pt;
            line-height: 1.4;
            color: #000;
            background: #eef2f7;
            margin: 0;
            padding: 20px 0;
        }

        .no-print-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #002855;
            color: #fff;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            font-family: system-ui, -apple-system, sans-serif;
        }

        .no-print-bar .title {
            font-weight: 700;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .no-print-bar .btn-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-print {
            background: #ffb800;
            color: #002855;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 13px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-print:hover {
            background: #e6a600;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .page-container {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 60px auto 30px auto;
            background: #ffffff;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
            page-break-after: always;
            break-after: page;
            position: relative;
        }

        .page-container:last-child {
            page-break-after: avoid;
            break-after: avoid;
        }

        .surat-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .surat-header h3 {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            text-decoration: underline;
            line-height: 1.3;
        }

        .identitas-table {
            width: 100%;
            border-collapse: collapse;
            margin-left: 20px;
            margin-bottom: 8px;
        }

        .identitas-table td {
            vertical-align: top;
            padding: 2px 0;
        }

        .col-label {
            width: 240px;
        }

        .col-colon {
            width: 15px;
            text-align: center;
        }

        .lampiran-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .lampiran-table td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: top;
        }

        .photo-placeholder-text {
            font-size: 9pt;
            color: #666;
            text-align: center;
            margin-top: 5px;
        }
        
        .signature-section {
            margin-top: 30px;
            width: 100%;
            display: table;
            table-layout: fixed;
        }
        
        .sig-title {
            font-weight: normal;
            margin-bottom: 4px;
        }
        
        .sig-space {
            height: 70px;
        }
        
        .sig-note {
            font-style: italic;
            font-size: 10pt;
            color: #444;
        }
        
        .sig-name {
            font-weight: bold;
        }

        @media print {
            body {
                background: none;
                margin: 0;
                padding: 0;
            }
            .no-print-bar {
                display: none !important;
            }
            .page-container {
                margin: 0;
                box-shadow: none;
                border: none;
                width: 100%;
                min-height: auto;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <div class="title">
            <i class="fas fa-file-image"></i>
            Lampiran Foto Calon Penerima BSPS
        </div>
        <div class="btn-group">
            <a href="javascript:history.back()" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <button class="btn-print" onclick="window.print()">
                <i class="fas fa-print"></i> Cetak Lampiran Foto
            </button>
        </div>
    </div>

    @foreach($items as $item)
        @php
            $tanggal = $item->tanggal_lahir;
            if ($tanggal) {
                try {
                    $tanggal = \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y');
                } catch (\Exception $e) {
                }
            }
            $ttl = ($item->tempat_lahir ? $item->tempat_lahir : '.....................') . ', ' . ($tanggal ? $tanggal : '.....................');
            
            $kotaCetak = '.....................';
            if ($item->kecamatan) {
                $isKota = in_array(strtoupper($item->kecamatan), ['KALIWATES', 'PATRANG', 'SUMBERSARI']);
                $kotaCetak = $isKota ? 'Jember' : ucwords(strtolower($item->desa_kelurahan ?: '.....................'));
            }

            $tglCetak = \Carbon\Carbon::now()->translatedFormat('d F Y');
        @endphp

        <!-- HALAMAN LAMPIRAN FOTO -->
        <div class="page-container">
            <div class="surat-header" style="margin-bottom: 20px;">
                <h3 style="text-decoration: none;">LAMPIRAN FOTO SURAT PERNYATAAN MENEMPATI RUMAH TIDAK LAYAK HUNI DAN TANAH</h3>
            </div>

            <table class="identitas-table" style="margin-left: 0;">
                <tr>
                    <td class="col-label">Nama</td>
                    <td class="col-colon">:</td>
                    <td><strong>{{ strtoupper($item->nama) }}</strong></td>
                </tr>
                <tr>
                    <td class="col-label">Nomor Induk Kependudukan (NIK)</td>
                    <td class="col-colon">:</td>
                    <td>{{ $item->no_ktp ?: '..................................................' }}</td>
                </tr>
                <tr>
                    <td class="col-label">tempat, tanggal lahir</td>
                    <td class="col-colon">:</td>
                    <td>{{ $ttl }}</td>
                </tr>
                <tr>
                    <td class="col-label">alamat</td>
                    <td class="col-colon">:</td>
                    <td>{{ $item->alamat ?: '..................................................' }}</td>
                </tr>
            </table>

            <div style="text-align: center; font-weight: bold; margin-top: 18px; margin-bottom: 12px; font-size: 11pt; line-height: 1.3;">
                FOTO KOMPONEN YANG TIDAK MEMENUHI ATAU TIDAK TERPENUHI SALAH SATU PERSYARATAN RUMAH LAYAK HUNI
            </div>

            <!-- TABEL FOTO KOMPONEN (3 KOLOM) -->
            <table class="lampiran-table">
                <tr>
                    <!-- Kolom 1 -->
                    <td style="width: 33.33%; text-align: center;">
                        <div style="min-height: 90px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                            @if($item->foto_bagian_dalam)
                                <img src="{{ asset($item->foto_bagian_dalam) }}" style="max-width: 100%; max-height: 100px; object-fit: contain; margin-bottom: 4px;">
                            @endif
                            <div class="photo-placeholder-text">
                                (Foto komponen rumah)<br>
                                (Keselamatan bangunan)
                            </div>
                        </div>
                        <div style="text-align: left; font-size: 9pt; line-height: 1.25; margin-top: 8px;">
                            Keselamatan bangunan:<br>
                            <table style="width: 100%; border: none; margin-top: 2px; font-size: 9pt; line-height: 1.25;">
                                <tr style="border: none;">
                                    <td style="width: 15px; border: none; padding: 0; vertical-align: top;">a.</td>
                                    <td style="border: none; padding: 0;">struktur bangunan;</td>
                                </tr>
                                <tr style="border: none;">
                                    <td style="border: none; padding: 0; vertical-align: top;">b.</td>
                                    <td style="border: none; padding: 0;">bahan penutup atap;<br>dan/atau</td>
                                </tr>
                                <tr style="border: none;">
                                    <td style="border: none; padding: 0; vertical-align: top;">c.</td>
                                    <td style="border: none; padding: 0;">dinding bangunan.</td>
                                </tr>
                            </table>
                        </div>
                    </td>

                    <!-- Kolom 2 -->
                    <td style="width: 33.33%; text-align: center; vertical-align: middle;">
                        <div style="min-height: 140px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                            @if($item->foto_sudut_kanan)
                                <img src="{{ asset($item->foto_sudut_kanan) }}" style="max-width: 100%; max-height: 120px; object-fit: contain; margin-bottom: 4px;">
                            @endif
                            <div class="photo-placeholder-text">
                                (Foto komponen rumah)<br>
                                (Keselamatan bangunan)
                            </div>
                        </div>
                    </td>

                    <!-- Kolom 3 -->
                    <td style="width: 33.33%; text-align: center;">
                        <div style="min-height: 90px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                            @if($item->foto_sudut_kiri)
                                <img src="{{ asset($item->foto_sudut_kiri) }}" style="max-width: 100%; max-height: 100px; object-fit: contain; margin-bottom: 4px;">
                            @endif
                            <div class="photo-placeholder-text">
                                (Foto komponen rumah)<br>
                                (Kesehatan penghuni)
                            </div>
                        </div>
                        <div style="text-align: left; font-size: 9pt; line-height: 1.25; margin-top: 8px;">
                            Kesehatan penghuni:<br>
                            <table style="width: 100%; border: none; margin-top: 2px; font-size: 9pt; line-height: 1.25;">
                                <tr style="border: none;">
                                    <td style="width: 15px; border: none; padding: 0; vertical-align: top;">a.</td>
                                    <td style="border: none; padding: 0;">sarana pencahayaan;</td>
                                </tr>
                                <tr style="border: none;">
                                    <td style="border: none; padding: 0; vertical-align: top;">b.</td>
                                    <td style="border: none; padding: 0;">penghawaan; dan/atau</td>
                                </tr>
                                <tr style="border: none;">
                                    <td style="border: none; padding: 0; vertical-align: top;">c.</td>
                                    <td style="border: none; padding: 0;">ketersediaan sarana utilitas bangunan (sarana MCK)</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- TABEL TAMPAK RUMAH (2x2 GRID) -->
            <table class="lampiran-table" style="margin-top: 15px;">
                <tr>
                    <td style="width: 50%; text-align: center; vertical-align: middle; height: 115px;">
                        @if($item->foto_sudut_depan)
                            <img src="{{ asset($item->foto_sudut_depan) }}" style="max-width: 100%; max-height: 95px; object-fit: contain; margin-bottom: 4px;"><br>
                        @endif
                        <span style="font-style: italic;">Tampak depan rumah</span>
                    </td>
                    <td style="width: 50%; text-align: center; vertical-align: middle; height: 115px;">
                        @if($item->foto_sudut_belakang)
                            <img src="{{ asset($item->foto_sudut_belakang) }}" style="max-width: 100%; max-height: 95px; object-fit: contain; margin-bottom: 4px;"><br>
                        @endif
                        <span style="font-style: italic;">Tampak belakang rumah</span>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%; text-align: center; vertical-align: middle; height: 115px;">
                        @if($item->foto_sudut_kiri || $item->foto_sudut_kanan)
                            <img src="{{ asset($item->foto_sudut_kiri ?: $item->foto_sudut_kanan) }}" style="max-width: 100%; max-height: 95px; object-fit: contain; margin-bottom: 4px;"><br>
                        @endif
                        <span style="font-style: italic;">Tampak Samping Rumah</span>
                    </td>
                    <td style="width: 50%; text-align: center; vertical-align: middle; height: 115px;">
                        @if($item->foto_bagian_dalam)
                            <img src="{{ asset($item->foto_bagian_dalam) }}" style="max-width: 100%; max-height: 95px; object-fit: contain; margin-bottom: 4px;"><br>
                        @endif
                        <span style="font-style: italic;">Sisi lain dari foto di atas/MCK</span>
                    </td>
                </tr>
            </table>

            <!-- TANDA TANGAN PENGUSUL -->
            <div class="signature-section" style="margin-top: 25px;">
                <div style="float: right; width: 250px; text-align: center;">
                    <div class="sig-title">{{ $kotaCetak }}, {{ $tglCetak }}</div>
                    <div class="sig-title">Yang mengusulkan,</div>
                    <div class="sig-space" style="height: 45px;"></div>
                    <div class="sig-note">tanda tangan</div>
                    <div class="sig-space" style="height: 15px;"></div>
                    <div class="sig-name">{{ strtoupper($item->nama) }}</div>
                </div>
                <div style="clear: both;"></div>
            </div>
        </div>
    @endforeach

    @if(isset($paginator) && $paginator->hasPages())
        <div class="no-print-pagination" style="text-align: center; padding: 20px; background: #fff; margin-top: 20px; border-top: 1px solid #eee;">
            <style>
                @media print { .no-print-pagination { display: none !important; } }
                .pagination { display: inline-flex; list-style: none; padding: 0; margin: 0; gap: 5px; }
                .pagination li { display: inline; }
                .pagination li a, .pagination li span { padding: 8px 12px; border: 1px solid #ddd; background: #fff; color: #333; text-decoration: none; border-radius: 4px; }
                .pagination li.active span { background: #002855; color: #fff; border-color: #002855; }
                .pagination li.disabled span { color: #aaa; background: #f9f9f9; }
            </style>
            {{ $paginator->links('pagination::bootstrap-4') }}
        </div>
    @endif

</body>
</html>
