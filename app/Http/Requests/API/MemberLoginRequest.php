<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

// MemberLoginRequest menangani validasi input saat jemaat melakukan login
class MemberLoginRequest extends FormRequest
{
    // Semua jemaat diizinkan mengakses endpoint login (publik)
    public function authorize(): bool
    {
        return true;
    }

    // API menangani validasi field id_jemaat dan password wajib diisi
    public function rules(): array
    {
        return [
            'id_jemaat' => 'required|string',
            'password'  => 'required|string',
        ];
    }

    // Pesan error dalam Bahasa Indonesia
    public function messages(): array
    {
        return [
            'id_jemaat.required' => 'ID Jemaat harus diisi',
            'password.required'  => 'Password harus diisi',
        ];
    }
}
