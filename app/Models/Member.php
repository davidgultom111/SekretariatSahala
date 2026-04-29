<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Member extends Model
{
    use HasFactory, SoftDeletes, HasApiTokens;

    protected $table = 'members';

    protected $fillable = [
        'id_jemaat',
        'nama_lengkap',
        'jenis_kelamin',
        'tanggal_lahir',
        'tempat_lahir',
        'alamat',
        'no_telepon',
        'status_aktif',
        'password',
        'role',
    ];

    /**
     * Sembunyikan password dari hasil API response
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast kolom ke tipe data yang sesuai
     */
    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // Relasi ke Surat
    public function letters()
    {
        return $this->hasMany(Letter::class, 'member_id');
    }

    // Relasi untuk Surat Pernikahan (Pria)
    public function marriageLettersAsPria()
    {
        return $this->hasMany(Letter::class, 'pria_id');
    }

    // Relasi untuk Surat Pernikahan (Wanita)
    public function marriageLettersAsWanita()
    {
        return $this->hasMany(Letter::class, 'wanita_id');
    }
}