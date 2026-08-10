<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bap extends Model
{
    protected $fillable = [
        'nomor_bap',
        'data_mingguan_id',
        'status',
        'catatan',
        'user_id',
    ];

    // Relasi ke DataMingguan
    public function dataMingguan()
    {
        return $this->belongsTo(DataMingguan::class, 'data_mingguan_id');
    }

    // Relasi ke User (yang generate)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate nomor BAP otomatis: BAP-001/PUPR/VII/2026
     */
    public static function generateNomor(): string
    {
        $bulanRomawi = [
            1=>'I',2=>'II',3=>'III',4=>'IV',5=>'V',6=>'VI',
            7=>'VII',8=>'VIII',9=>'IX',10=>'X',11=>'XI',12=>'XII',
        ];
        $bulan = $bulanRomawi[(int) date('n')];
        $tahun = date('Y');

        // Hitung urutan BAP bulan ini
        $count = self::whereYear('created_at', $tahun)
                     ->whereMonth('created_at', date('n'))
                     ->count();
        $urutan = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        return "BAP-{$urutan}/PUPR/{$bulan}/{$tahun}";
    }

    // Label warna status
    public function statusColor(): string
    {
        return match($this->status) {
            'terbit'  => 'success',
            'ttd'     => 'info',
            'revisi'  => 'warning',
            default   => 'secondary', // draft
        };
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'terbit'  => 'Terbit',
            'ttd'     => 'Ditandatangani',
            'revisi'  => 'Revisi',
            default   => 'Draft',
        };
    }
}
