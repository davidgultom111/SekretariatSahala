<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_lengkap',
        'jenis_kelamin',
        'tanggal_lahir',
        'tempat_lahir',
        'alamat',
        'no_telepon',
        'status_aktif',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    /**
     * Get the letters for the member.
     */
    public function letters()
    {
        return $this->hasMany(Letter::class);
    }

    /**
     * Get the marriage letters where member is pria.
     */
    public function marriageLettersAsPria()
    {
        return $this->hasMany(Letter::class, 'member_pria_id');
    }

    /**
     * Get the marriage letters where member is wanita.
     */
    public function marriageLettersAsWanita()
    {
        return $this->hasMany(Letter::class, 'member_wanita_id');
    }
}
