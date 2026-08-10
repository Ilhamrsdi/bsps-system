<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $table = 'surveys';

    protected $fillable = [
        'data_mingguan_id',
        'nama_kegiatan',
        'nama_petugas_1',
        'nama_petugas_2',
        'tanggal_survei',
        'user_id',
        'nama_pemohon',
        'nik_pemohon',
        'alamat_pemohon',
        'jenis_bangunan',
        'fungsi_bangunan',
        'jumlah_lantai',
        'tinggi_bangunan',
        'luas_bangunan',
        'luas_tanah',
        'status_hak_tanah',
        'kecamatan',
        'desa_kelurahan',
        'nama_jalan',
        'alamat_lokasi',
        'latitude',
        'longitude',
        'item_admin',
        'catatan_admin',
        'foto_admin_1',
        'foto_admin_2',
        'foto_admin_3',
        'item_fungsi',
        'catatan_fungsi',
        'foto_fungsi_1',
        'foto_fungsi_2',
        'foto_fungsi_3',
        'item_peruntukan',
        'catatan_peruntukan',
        'foto_peruntukan_1',
        'foto_peruntukan_2',
        'foto_peruntukan_3',
        'item_tata',
        'catatan_tata',
        'foto_tata_1',
        'foto_tata_2',
        'foto_tata_3',
        'item_kelaikan',
        'catatan_kelaikan',
        'foto_kelaikan_1',
        'foto_kelaikan_2',
        'foto_kelaikan_3',
        'garis_sempadan_tritis',
        'jarak_as_jalan',
        'pelanggaran_sempadan',
        'catatan_survei',
        'foto_bangunan',
        'foto_akses',
    ];

    protected $casts = [
        'tanggal_survei'         => 'date',
        'jumlah_lantai'          => 'integer',
        'tinggi_bangunan'        => 'float',
        'luas_bangunan'          => 'float',
        'luas_tanah'             => 'float',
        'garis_sempadan_tritis'  => 'float',
        'jarak_as_jalan'         => 'float',
        'pelanggaran_sempadan'   => 'float',
    ];

    /**
     * Relasi ke Data Mingguan / Kegiatan Lapangan
     */
    public function dataMingguan()
    {
        return $this->belongsTo(DataMingguan::class, 'data_mingguan_id');
    }

    /**
     * Relasi ke User / Petugas Penginput
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
