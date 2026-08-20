<?php

namespace App\Console\Commands;

use App\Models\DataPenerima;
use Illuminate\Console\Command;
use ZipArchive;

class SyncMypkpData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mypkp:sync {file? : Path to the excel file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi data NIK calon penerima yang telah diusulkan di myPKP';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file') ?: 'C:/Users/ilham/Downloads/export_data_layak (1) (2).xlsx';

        if (!file_exists($file)) {
            $this->error("File Excel tidak ditemukan di: {$file}");
            return 1;
        }

        $this->info("Membaca file Excel: {$file}");

        $zip = new ZipArchive();
        if ($zip->open($file) !== true) {
            $this->error("Gagal membuka file Excel (ZIP format).");
            return 1;
        }

        // Baca shared strings
        $sharedStrings = [];
        if (($index = $zip->locateName('xl/sharedStrings.xml')) !== false) {
            $xml = simplexml_load_string($zip->getFromIndex($index));
            foreach ($xml->si as $si) {
                $text = '';
                if (isset($si->t)) {
                    $text .= (string)$si->t;
                } elseif (isset($si->r)) {
                    foreach ($si->r as $r) {
                        $text .= (string)$r->t;
                    }
                }
                $sharedStrings[] = $text;
            }
        }

        // Baca sheet1
        $sheetXml = simplexml_load_string($zip->getFromName('xl/worksheets/sheet1.xml'));
        $rows = [];
        foreach ($sheetXml->sheetData->row as $row) {
            $r = [];
            foreach ($row->c as $c) {
                $attr = $c->attributes();
                $type = (string)$attr['t'];
                $val = (string)$c->v;
                if ($type === 's') {
                    $val = $sharedStrings[(int)$val] ?? '';
                } elseif ($type === 'inlineStr') {
                    $val = (string)$c->is->t;
                }
                $r[] = $val;
            }
            $rows[] = $r;
        }
        $zip->close();

        $headers = $rows[0] ?? [];
        $nikIdx = -1;
        foreach ($headers as $idx => $h) {
            if (stripos($h, 'nik') !== false) {
                $nikIdx = $idx;
                break;
            }
        }

        if ($nikIdx === -1) {
            $nikIdx = 4; // default column NIK
        }

        $nikList = [];
        for ($i = 1; $i < count($rows); $i++) {
            $rawNik = trim((string)($rows[$i][$nikIdx] ?? ''));
            $cleanNik = ltrim($rawNik, "'");
            if (!empty($cleanNik)) {
                $nikList[] = $cleanNik;
            }
        }

        $uniqueNiks = array_values(array_unique($nikList));
        $this->info("Ditemukan " . count($uniqueNiks) . " NIK unik di file Excel.");

        // Reset status myPKP sebelumnya jika diperlukan, lalu set yang ada di Excel
        DataPenerima::where('is_mypkp', true)->update(['is_mypkp' => false]);

        // Update in chunks
        $chunks = array_chunk($uniqueNiks, 500);
        $totalUpdated = 0;

        foreach ($chunks as $chunk) {
            $updated = DataPenerima::whereIn('no_ktp', $chunk)->update(['is_mypkp' => true]);
            $totalUpdated += $updated;
        }

        $this->info("Berhasil mengupdate {$totalUpdated} data penerima sebagai terdaftar myPKP (is_mypkp = 1).");

        return 0;
    }
}
