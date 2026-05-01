<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// MemberResource menangani format output data jemaat pada semua response API
class MemberResource extends JsonResource
{
    // API menangani transformasi model Member menjadi array JSON yang dikirim ke client
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'id_jemaat'     => $this->id_jemaat,
            'nama_lengkap'  => $this->nama_lengkap,
            'jenis_kelamin' => $this->jenis_kelamin,
            // Format tanggal lahir ke Y-m-d agar konsisten di semua client
            'tanggal_lahir' => $this->tanggal_lahir?->format('Y-m-d'),
            'tempat_lahir'  => $this->tempat_lahir,
            'alamat'        => $this->alamat,
            'no_telepon'    => $this->no_telepon,
            'status_aktif'  => $this->status_aktif,
            'role'          => $this->role,
            // Format timestamp ke ISO 8601 agar mudah diparse oleh frontend dan mobile
            'created_at'    => $this->created_at?->toIso8601String(),
            'updated_at'    => $this->updated_at?->toIso8601String(),
        ];
    }
}
