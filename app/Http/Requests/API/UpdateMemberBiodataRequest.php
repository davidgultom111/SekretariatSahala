<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

// UpdateMemberBiodataRequest menangani validasi saat jemaat update biodata sendiri
class UpdateMemberBiodataRequest extends FormRequest
{
    // Jemaat yang sudah login diizinkan update biodatanya sendiri
    public function authorize(): bool
    {
        return true;
    }

    // API menangani validasi field biodata yang boleh diubah oleh jemaat (bukan admin)
    public function rules(): array
    {
        return [
            // Field 'sometimes' berarti opsional — hanya divalidasi jika dikirim
            'nama_lengkap' => 'sometimes|string|max:255',
            'alamat'       => 'sometimes|string|max:500',
            'no_telepon'   => 'sometimes|string|max:20',
        ];
    }

    // Pesan error dalam Bahasa Indonesia
    public function messages(): array
    {
        return [
            'nama_lengkap.max' => 'Nama lengkap maksimal 255 karakter',
            'no_telepon.max'   => 'Nomor telepon maksimal 20 karakter',
        ];
    }
}
