<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Model Member merepresentasikan data jemaat GPdI di database
class Member extends Model
{
    use HasFactory, SoftDeletes, HasApiTokens;

    protected $table = 'members';

    // Field yang boleh diisi secara massal (mass assignment)
    protected $fillable = [
        'id_jemaat',       // format DDMMYYYY, di-generate otomatis oleh MemberObserver
        'nama_lengkap',
        'jenis_kelamin',   // 'Laki-laki' atau 'Perempuan'
        'tanggal_lahir',
        'tempat_lahir',
        'alamat',
        'no_telepon',
        'status_aktif',    // 'Aktif' atau 'Tidak Aktif'
        'password',
        'role',            // 'admin' atau 'member'
    ];

    // Sembunyikan password dari hasil API response agar tidak bocor ke client
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Cast kolom ke tipe data yang sesuai saat diakses dari model
    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // Relasi: satu jemaat bisa memiliki banyak surat
    public function letters(): HasMany
    {
        return $this->hasMany(Letter::class, 'member_id');
    }

    // Relasi: surat pernikahan di mana jemaat ini sebagai mempelai pria
    public function marriageLettersAsPria(): HasMany
    {
        return $this->hasMany(Letter::class, 'member_pria_id');
    }

    // Relasi: surat pernikahan di mana jemaat ini sebagai mempelai wanita
    public function marriageLettersAsWanita(): HasMany
    {
        return $this->hasMany(Letter::class, 'member_wanita_id');
    }

    // Relasi: satu jemaat bisa memiliki banyak pengajuan surat
    public function pengajuans(): HasMany
    {
        return $this->hasMany(PengajuanSurat::class, 'member_id');
    }

    // Relasi: pengajuan pernikahan di mana jemaat ini sebagai mempelai pria
    public function pengajuanPernikahanAsPria(): HasMany
    {
        return $this->hasMany(PengajuanSurat::class, 'member_pria_id');
    }

    // Relasi: pengajuan pernikahan di mana jemaat ini sebagai mempelai wanita
    public function pengajuanPernikahanAsWanita(): HasMany
    {
        return $this->hasMany(PengajuanSurat::class, 'member_wanita_id');
    }
}
