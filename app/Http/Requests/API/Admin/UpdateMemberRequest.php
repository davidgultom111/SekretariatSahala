<?php

namespace App\Http\Requests\API\Admin;

use Illuminate\Foundation\Http\FormRequest;

// UpdateMemberRequest menangani validasi input saat admin update data jemaat
class UpdateMemberRequest extends FormRequest
{
    // Hanya admin yang sudah login yang bisa mengakses (dijaga oleh middleware role:admin)
    public function authorize(): bool
    {
        return true;
    }

    // API menangani validasi field saat update jemaat (semua opsional dengan 'sometimes')
    public function rules(): array
    {
        return [
            // 'sometimes' berarti field hanya divalidasi jika dikirim dalam request
            'nama_lengkap'  => 'sometimes|string|max:255',
            'jenis_kelamin' => 'sometimes|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'sometimes|date|before:today',
            'tempat_lahir'  => 'sometimes|string|max:255',
            'alamat'        => 'sometimes|string|max:500',
            'no_telepon'    => 'sometimes|string|max:20',
            // Hanya dua status yang valid setelah penghapusan status Dipindahkan
            'status_aktif'  => 'sometimes|in:Aktif,Tidak Aktif',
            'password'      => 'sometimes|string|min:6',
        ];
    }

    // Pesan error dalam Bahasa Indonesia
    public function messages(): array
    {
        return [
            'jenis_kelamin.in' => 'Jenis kelamin harus Laki-laki atau Perempuan',
            'status_aktif.in'  => 'Status harus: Aktif atau Tidak Aktif',
            'password.min'     => 'Password minimal 6 karakter',
        ];
    }
}
