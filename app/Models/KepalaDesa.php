<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KepalaDesa extends Model
{
    protected $table = 'kepala_desas';

    protected $fillable = [
        'kecamatan',
        'desa_kelurahan',
        'jabatan',
        'nama',
        'nomor_telepon',
    ];
}
