<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPenerima extends Model
{
    use HasFactory;

    protected $guarded = [];

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
     * Scope: Data yang SUDAH survei / diverifikasi (seluruh 18 field wajib terisi atau status khusus).
     */
    public function scopeSudahSurvei($query)
    {
        $fields = self::$fieldWajibSurvei;
        return $query->where(function ($q) use ($fields) {
            $q->whereIn('status', ['meninggal', 'pindah', 'tidak diketahui'])
              ->orWhere(function ($sub) use ($fields) {
                  foreach ($fields as $field) {
                      $sub->whereNotNull($field);
                      if ($field !== 'tanggal_lahir') {
                          $sub->where($field, '!=', '');
                      }
                  }
              });
        });
    }

    /**
     * Scope: Data yang BELUM survei (ada field wajib yang belum terisi).
     */
    public function scopeBelumSurvei($query)
    {
        $fields = self::$fieldWajibSurvei;
        return $query->where(function ($q) use ($fields) {
            $q->whereNull('status')
              ->orWhereNotIn('status', ['meninggal', 'pindah', 'tidak diketahui']);
        })->where(function ($q) use ($fields) {
            foreach ($fields as $field) {
                $q->orWhereNull($field);
                if ($field !== 'tanggal_lahir') {
                    $q->orWhere($field, '=', '');
                }
            }
        });
    }

    /**
     * Helper: Cek apakah satu record ini sudah lengkap surveinya atau memiliki status verifikasi khusus.
     */
    public function isSudahSurvei(): bool
    {
        if (in_array($this->status, ['meninggal', 'pindah', 'tidak diketahui'])) {
            return true;
        }

        foreach (self::$fieldWajibSurvei as $field) {
            if (empty($this->{$field})) return false;
        }
        return true;
    }

    /**
     * Relasi ke Akun Petugas Verval (User)
     */
    public function petugas()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

