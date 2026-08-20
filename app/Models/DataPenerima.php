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
        $conds = [];
        foreach (self::$fieldWajibSurvei as $field) {
            $conds[] = "({$field} IS NOT NULL AND TRIM({$field}) != '')";
        }
        $formLengkapSql = "(" . implode(" AND ", $conds) . ")";
        $nikKhususSql = "'" . implode("','", self::$nikKhususSelesai) . "'";

        return "(status IN ('meninggal', 'pindah', 'tidak diketahui', 'menolak disurvey') OR no_ktp IN ({$nikKhususSql}) OR {$formLengkapSql})";
    }

    /**
     * Scope: Data yang SUDAH survei / diverifikasi (seluruh 18 field wajib terisi, status khusus, atau NIK khusus).
     */
    public function scopeSudahSurvei($query)
    {
        $fields = self::$fieldWajibSurvei;
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

        return $query->where(function ($q) use ($fields, $fotoFields) {
            $q->whereIn('status', ['meninggal', 'pindah', 'tidak diketahui', 'menolak disurvey'])
              ->orWhereIn('no_ktp', self::$nikKhususSelesai)
              ->orWhere(function ($sub) use ($fields, $fotoFields) {
                  foreach ($fields as $field) {
                      $sub->whereNotNull($field);
                      if ($field !== 'tanggal_lahir') {
                          $sub->where($field, '!=', '');
                      }
                  }
                  // Pastikan tidak ada foto yang ditolak
                  foreach ($fotoFields as $ff) {
                      $sub->where(function ($qff) use ($ff) {
                          $qff->whereNull($ff)->orWhere($ff, '!=', 'tidak layak');
                      });
                  }
              });
        });
    }

    /**
     * Scope: Data yang BELUM survei (ada field wajib yang belum terisi) ATAU REVISI (ada foto ditolak), kecuali NIK khusus.
     */
    public function scopeBelumSurvei($query)
    {
        $fields = self::$fieldWajibSurvei;
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

        return $query->where(function ($q) use ($fields, $fotoFields) {
            $q->whereNull('status')
              ->orWhereNotIn('status', ['meninggal', 'pindah', 'tidak diketahui', 'menolak disurvey']);
        })->whereNotIn('no_ktp', self::$nikKhususSelesai)
          ->where(function ($q) use ($fields, $fotoFields) {
            $q->where(function ($sub1) use ($fields) {
                foreach ($fields as $field) {
                    $sub1->orWhereNull($field);
                    if ($field !== 'tanggal_lahir') {
                        $sub1->orWhere($field, '=', '');
                    }
                }
            })->orWhere(function ($sub2) use ($fotoFields) {
                foreach ($fotoFields as $ff) {
                    $sub2->orWhere($ff, '=', 'tidak layak');
                }
            });
        });
    }

    /**
     * Helper: Cek apakah satu record ini sudah lengkap surveinya atau memiliki status verifikasi khusus / NIK khusus.
     */
    public function isSudahSurvei(): bool
    {
        if (in_array($this->no_ktp, self::$nikKhususSelesai)) {
            return true;
        }

        if (in_array($this->status, ['meninggal', 'pindah', 'tidak diketahui', 'menolak disurvey'])) {
            return true;
        }

        foreach (self::$fieldWajibSurvei as $field) {
            if (empty($this->{$field})) return false;
        }

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
            if ($this->{$ff} === 'tidak layak') return false;
        }

        return true;
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

