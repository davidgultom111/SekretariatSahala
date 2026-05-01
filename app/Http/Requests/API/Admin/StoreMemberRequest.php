<?php

namespace App\Http\Requests\API\Admin;

use Illuminate\Foundation\Http\FormRequest;

// StoreMemberRequest menangani validasi input saat admin create jemaat baru
class StoreMemberRequest extends FormRequest
{
    // Hanya admin yang sudah login yang bisa mengakses (dijaga oleh middleware role:admin)
    public function authorize(): bool
    {
        return true;
    }

    // API menangani validasi semua field wajib saat create jemaat
    public function rules(): array
    {
        return [
            'nama_lengkap'  => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'required|date|before:today',
            'tempat_lahir'  => 'required|string|max:255',
            'alamat'        => 'required|string|max:500',
            'no_telepon'    => 'required|string|max:20',
            // Hanya dua status yang valid setelah penghapusan status Dipindahkan
            'status_aktif'  => 'required|in:Aktif,Tidak Aktif',
        ];
    }

    // Pesan error dalam Bahasa Indonesia
    public function messages(): array
    {
        return [
            'jenis_kelamin.in'     => 'Jenis kelamin harus Laki-laki atau Perempuan',
            'status_aktif.in'      => 'Status harus: Aktif atau Tidak Aktif',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini',
        ];
    }
}
