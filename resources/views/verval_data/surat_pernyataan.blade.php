<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pernyataan Calon Penerima BSPS</title>
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

        .identitas-table, .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-left: 20px;
            margin-bottom: 8px;
        }

        .identitas-table td, .detail-table td {
            vertical-align: top;
            padding: 2px 0;
        }

        .col-label {
            width: 220px;
        }

        .col-colon {
            width: 15px;
            text-align: center;
        }

        ol.poin-list {
            margin: 0 0 12px 0;
            padding-left: 22px;
            list-style-type: lower-alpha;
        }

        ol.poin-list > li {
            margin-bottom: 6px;
            text-align: justify;
        }

        .sub-detail {
            margin-left: 10px;
            margin-top: 4px;
            margin-bottom: 6px;
        }

        .signature-section {
            margin-top: 30px;
            width: 100%;
            display: table;
            table-layout: fixed;
        }

        .sig-col {
            display: table-cell;
            vertical-align: top;
            text-align: center;
            width: 50%;
        }

        .sig-space {
            height: 70px;
        }

        .sig-title {
            font-weight: normal;
            margin-bottom: 4px;
        }

        .sig-name {
            font-weight: bold;
        }

        .sig-note {
            font-style: italic;
            font-size: 10pt;
            color: #444;
        }

        .lampiran-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .lampiran-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }

        .photo-placeholder-text {
            font-style: italic;
            font-size: 9.5pt;
            color: #000;
            line-height: 1.3;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
                margin: 0;
            }

            .no-print-bar {
                display: none !important;
            }

            .page-container {
                margin: 0;
                padding: 0;
                box-shadow: none;
                width: 100%;
                min-height: auto;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <div class="title">
            <i class="fas fa-file-signature" style="color: #ffb800;"></i>
            <span>Cetak Surat Pernyataan BSPS (Total: {{ count($items) }} Dokumen)</span>
        </div>
        <div class="btn-group">
            <a href="{{ route('data-verval') }}" onclick="return handleKembali(event);" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <button onclick="window.print()" class="btn-print">
                <i class="fas fa-print"></i> Cetak / Print Dokumen
            </button>
        </div>
    </div>

    <script>
        function handleKembali(e) {
            if (e) e.preventDefault();

            // 1. Jika dibeli/dibuka di tab baru (window.opener atau history.length <= 1)
            if (window.opener || window.history.length <= 1) {
                window.close();
                // Jika browser memblokir window.close(), lakukan redirect
                setTimeout(function() {
                    if (document.referrer && document.referrer !== window.location.href) {
                        window.location.href = document.referrer;
                    } else {
                        window.location.href = "{{ route('data-verval') }}";
                    }
                }, 150);
                return false;
            }

            // 2. Jika navigasi di tab yang sama (history > 1)
            if (window.history.length > 1) {
                window.history.back();
                return false;
            }

            // 3. Fallback
            window.location.href = document.referrer || "{{ route('data-verval') }}";
            return false;
        }
    </script>

    @foreach($items as $item)
        @php
            // Nama Bulan Indonesia
            $bulanId = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

            // Format Tanggal Lahir
            $tglLahir = '..................................';
            if ($item->tanggal_lahir) {
                try {
                    $dt = \Carbon\Carbon::parse($item->tanggal_lahir);
                    $tglLahir = $dt->day . ' ' . $bulanId[$dt->month] . ' ' . $dt->year;
                } catch (\Exception $e) {}
            }

            // Tempat, Tanggal Lahir
            $tempatLahir = $item->tempat_lahir ? strtoupper($item->tempat_lahir) : '..................................';
            $ttl = $tempatLahir . ', ' . $tglLahir;

            // Penghasilan — format Rupiah
            $penghasilan = '..................................';
            if ($item->penghasilan) {
                $val = trim((string)$item->penghasilan);
                if (is_numeric($val)) {
                    $penghasilan = 'Rp ' . number_format((float)$val, 0, ',', '.');
                } else {
                    $digitsOnly = preg_replace('/[^0-9]/', '', $val);
                    if ($digitsOnly !== '' && is_numeric($digitsOnly)) {
                        $penghasilan = 'Rp ' . number_format((float)$digitsOnly, 0, ',', '.');
                    } else {
                        $penghasilan = $val;
                    }
                }
            }

            // Luas Tanah
            $luasTanah = $item->luas_tanah ? $item->luas_tanah . ' m²' : '.......... m²';

            // Jenis Kepemilikan Lahan
            $kepemilikan = $item->jenis_kepemilikan_lahan ?: '..................................';

            // Telah Ditempati Selama
            $ditempati = $item->telah_ditempati_selama ? $item->telah_ditempati_selama . ' tahun' : '.......... tahun';

            // Tanggal Cetak (hari ini)
            $now = \Carbon\Carbon::now();
            $tglCetak = $now->day . ' ' . $bulanId[$now->month] . ' ' . $now->year;
            $kotaCetak = $item->kabupaten_kota
                ? ucwords(strtolower(str_replace(['KAB. ', 'KOTA ', 'KAB '], '', $item->kabupaten_kota)))
                : 'Jember';
        @endphp

        <div class="page-container">
            <div class="surat-header">
                <h3>SURAT PERNYATAAN MENEMPATI RUMAH TIDAK LAYAK HUNI DAN TANAH</h3>
            </div>

            <p style="margin-bottom: 6px;">Saya yang bertanda tangan :</p>
            <table class="identitas-table">
                <tr>
                    <td class="col-label">nama</td>
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
                    <td class="col-label">jenis kelamin</td>
                    <td class="col-colon">:</td>
                    <td>{{ $item->jenis_kelamin ? ucfirst(strtolower($item->jenis_kelamin)) : '..................................................' }}</td>
                </tr>
                <tr>
                    <td class="col-label">alamat</td>
                    <td class="col-colon">:</td>
                    <td>{{ $item->alamat ?: '..................................................' }}</td>
                </tr>
            </table>

            <p style="margin-top: 10px; margin-bottom: 6px;">menyatakan bahwa saya warga negara Indonesia:</p>
            <ol class="poin-list">
                <li>
                    menempati rumah tidak layak huni dan tanah satu-satunya yang terletak di:
                    <div class="sub-detail">
                        <table class="detail-table">
                            <tr>
                                <td style="width: 170px;">jalan, RT/RW</td>
                                <td class="col-colon">:</td>
                                <td>{{ $item->alamat ?: '..................................................' }}</td>
                            </tr>
                            <tr>
                                <td>kelurahan, kecamatan</td>
                                <td class="col-colon">:</td>
                                <td>{{ $item->desa_kelurahan ?: '....................' }}, {{ $item->kecamatan ? 'KEC. ' . $item->kecamatan : '....................' }}</td>
                            </tr>
                            <tr>
                                <td>kota</td>
                                <td class="col-colon">:</td>
                                <td>{{ $item->kabupaten_kota ?: 'KAB. JEMBER' }}</td>
                            </tr>
                        </table>
                        <div style="margin-top: 4px;">
                            dengan<br>
                            <table class="detail-table">
                                <tr>
                                    <td style="width: 170px;">luas tanah</td>
                                    <td class="col-colon">:</td>
                                    <td>{{ $luasTanah }}</td>
                                </tr>
                                <tr>
                                    <td>status kepemilikan tanah</td>
                                    <td class="col-colon">:</td>
                                    <td>{{ $kepemilikan }}, tidak dalam sengketa</td>
                                </tr>
                                <tr>
                                    <td>telah ditempati selama</td>
                                    <td class="col-colon">:</td>
                                    <td>{{ $ditempati }} <em>(minimal 1 (satu) tahun)</em></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </li>
                <li>
                    memiliki penghasilan sendiri jika lajang atau penghasilan bersama suami-istri jika berpasangan*), rata-rata sebesar <strong>{{ $penghasilan }}</strong> per bulan;
                </li>
                <li>
                    belum pernah menerima Bantuan Stimulan Perumahan Swadaya (BSPS), Bedah Rumah, atau program kemudahan dan bantuan pembiayaan perumahan dari pemerintah dalam 5 (lima) tahun terakhir, kecuali karena bencana, kebakaran, dan/atau penataan kumuh;
                </li>
                <li>
                    bersedia menerima dan akan menggunakan dana Bedah Rumah sesuai hukum;
                </li>
                <li>
                    akan menghuni rumah yang telah direnovasi melalui Bedah Rumah selama 3 (tiga) tahun kecuali karena alasan yang sah;
                </li>
                <li>
                    bersedia diperiksa oleh petugas/pejabat yang berwenang; dan
                </li>
                <li>
                    bersedia dikenai sanksi jika memberikan data dan informasi yang tidak sesuai dengan fakta/hukum.
                </li>
            </ol>

            <p style="text-align: justify; text-indent: 30px; margin-top: 14px; margin-bottom: 20px;">
                Pernyataan ini dan lampirannya saya buat dengan sebenarnya dengan penuh tanggung jawab dan tanpa paksaan.
            </p>

            <div class="signature-section">
                <div class="sig-col">
                    <div class="sig-title">Mengetahui,</div>
                    <div class="sig-title">{{ trim(preg_replace('/\bPAW\b/i', '', $item->jabatan_kades)) ?: 'Kepala Desa/Lurah/Nama lain yang setingkat' }},</div>
                    <div class="sig-space"></div>
                    <div class="sig-note">tanda tangan dan stempel</div>
                    <div class="sig-space" style="height: 15px;"></div>
                    <div class="sig-name">( {{ $item->nama_kades ?: '..................................................' }} )</div>
                    <div class="sig-note">nama tanpa gelar</div>
                </div>
                <div class="sig-col">
                    <div class="sig-title">{{ $kotaCetak }}, {{ $tglCetak }}</div>
                    <div class="sig-title">Calon Penerima Bantuan,</div>
                    <div class="sig-space"></div>
                    <div class="sig-note">tanda tangan/cap jari</div>
                    <div class="sig-space" style="height: 15px;"></div>
                    <div class="sig-name">( {{ strtoupper($item->nama) }} )</div>
                    <div class="sig-note">nama tanpa gelar</div>
                </div>
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
