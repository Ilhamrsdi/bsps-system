<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DataMingguan extends Model
{
    use HasFactory;

    protected $table = 'data_mingguans';

    protected $fillable = [
        'nama_kegiatan',
        'nama_pemohon',
        'nik_pemohon',
        'lokasi',
        'alamat',
        'tanggal',
        'minggu',
        'status',
        'status_bap',
        'nilai_kontrak',
        'kontraktor',
        'deskripsi',
        'user_id',
    ];

    protected $casts = [
        'tanggal'  => 'date',
        'minggu'   => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke BAP (satu kegiatan punya satu BAP)
    public function bap()
    {
        return $this->hasOne(Bap::class, 'data_mingguan_id');
    }

    // Relasi ke Petugas Survei (banyak petugas per kegiatan penugasan)
    public function petugas()
    {
        return $this->belongsToMany(User::class, 'penugasans', 'data_mingguan_id', 'user_id')->withTimestamps();
    }

    // Relasi ke Hasil Form Survei Lapangan
    public function surveys()
    {
        return $this->hasMany(Survey::class, 'data_mingguan_id');
    }

    // Helper: warna badge status
    public function statusColor(): string
    {
        return match($this->status) {
            'selesai'  => 'success',
            'proses'   => 'warning',
            'survei'   => 'info',
            'batal'    => 'danger',
            default    => 'secondary',
        };
    }

    // Helper: nilai kontrak diformat
    public function nilaiKontrakFormatted(): string
    {
        if (!$this->nilai_kontrak) return '-';
        $raw = preg_replace('/\D/', '', $this->nilai_kontrak);
        return 'Rp ' . number_format((int)$raw, 0, ',', '.');
    }
}
