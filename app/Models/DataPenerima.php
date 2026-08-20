<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPenerima extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_mypkp' => 'boolean',
    ];

    /**
     * Scope: Data yang telah diusulkan di myPKP.
     */
    public function scopeMypkp($query)
    {
        return $query->where('is_mypkp', true);
    }

    /**
     * Daftar kolom WAJIB yang harus terisi agar dianggap "Sudah Survei / Selesai".
     * (Catatan: latitude & longitude/peta lokasi dikecualikan karena bisa menyusul).
     */
    public static array $fieldWajibSurvei = [
        // Data Tambahan & Kelaikan Hunian
        'tempat_lahir',
        'tanggal_lahir',
        'luas_tanah',
        'telah_ditempati_selama',
        'status_tanah',
        // Indikator Kelayakan RTLH (6 indikator)
        'indikator_lantai',
        'indikator_pondasi',
        'indikator_dinding',
        'indikator_struktur',
        'indikator_atap',
        'indikator_penghasilan',
        // Foto Fisik Rumah (5 sudut)
        'foto_sudut_depan',
        'foto_sudut_belakang',
        'foto_bagian_dalam',
        'foto_sudut_kiri',
        'foto_sudut_kanan',
        // Berkas Dokumen Administrasi
        'ktp',
        'kk',
        'surat_pernyataan',
        // 'latitude' & 'longitude' dikecualikan dari syarat wajib
    ];

    /**
     * Daftar NIK khusus yang statusnya dianggap Selesai / Sudah Survei.
     */
    public static array $nikKhususSelesai = [
        '3509072812800002',
        '3509073103530001',
        '3509070208730001',
    ];

    /**
     * Helper: Mendapatkan ekspresi SQL untuk kondisi Sudah Survei / Selesai.
     */
    public static function getSudahSql(): string
    {
        return "(status IN ('ditemukan', 'meninggal', 'pindah', 'tidak diketahui', 'menolak disurvey'))";
    }

    /**
     * Scope: Data yang SUDAH survei / diverifikasi di lapangan.
     */
    public function scopeSudahSurvei($query)
    {
        return $query->whereIn('status', ['ditemukan', 'meninggal', 'pindah', 'tidak diketahui', 'menolak disurvey']);
    }

    /**
     * Scope: Data yang BELUM survei / belum verval di lapangan.
     */
    public function scopeBelumSurvei($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('status')
              ->orWhere('status', '')
              ->orWhereNotIn('status', ['ditemukan', 'meninggal', 'pindah', 'tidak diketahui', 'menolak disurvey']);
        });
    }

    /**
     * Helper: Cek apakah satu record ini sudah lengkap surveinya atau memiliki status verifikasi di lapangan.
     */
    public function isSudahSurvei(): bool
    {
        return in_array($this->status, ['ditemukan', 'meninggal', 'pindah', 'tidak diketahui', 'menolak disurvey']);
    }

    /**
     * Helper: Cek apakah ada indikator/foto yang ditolak (Revisi).
     */
    public function isRevisi(): bool
    {
        $fotoFields = [
            'status_foto_sudut_depan',
            'status_foto_sudut_belakang',
            'status_foto_bagian_dalam',
            'status_foto_sudut_kiri',
            'status_foto_sudut_kanan',
            'status_ktp',
            'status_kk',
            'status_surat_pernyataan'
        ];
        foreach ($fotoFields as $ff) {
            if ($this->{$ff} === 'tidak layak') return true;
        }
        return false;
    }

    /**
     * Relasi ke Akun Petugas Verval (User)
     */
    public function petugas()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

