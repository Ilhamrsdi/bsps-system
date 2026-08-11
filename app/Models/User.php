<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'plain_password',
        'nip',
        'jabatan',
        'kecamatan',
        'desa',
        'phone',
        'status',
        'role',
        'latitude',
        'longitude',
        'last_ip',
        'device_type',
        'user_agent',
        'last_location_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_location_at'  => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Cek apakah user memiliki role admin (Super Admin).
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user memiliki role admin kecamatan.
     */
    public function isAdminKecamatan(): bool
    {
        return $this->role === 'admin_kecamatan';
    }

    /**
     * Cek apakah user merupakan Admin Kabupaten/Super Admin atau Admin Kecamatan.
     */
    public function isAdminOrKecamatan(): bool
    {
        return in_array($this->role, ['admin', 'admin_kecamatan']);
    }

    /**
     * Cek apakah user memiliki role petugas.
     */
    public function isPetugas(): bool
    {
        return $this->role === 'petugas';
    }

    /**
     * Relasi ke kegiatan yang ditugaskan.
     */
    public function kegiatans()
    {
        return $this->belongsToMany(DataMingguan::class, 'penugasans', 'user_id', 'data_mingguan_id')->withTimestamps();
    }

    /**
     * Relasi ke hasil survei yang diisi oleh user/petugas.
     */
    public function surveys()
    {
        return $this->hasMany(Survey::class, 'user_id');
    }
}
