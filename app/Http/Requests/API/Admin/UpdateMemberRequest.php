<?php

namespace App\Http\Requests\API\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_lengkap'  => 'sometimes|string|max:255',
            'jenis_kelamin' => 'sometimes|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'sometimes|date|before:today',
            'tempat_lahir'  => 'sometimes|string|max:255',
            'alamat'        => 'sometimes|string|max:500',
            'no_telepon'    => 'sometimes|string|max:20',
            'status_aktif'  => 'sometimes|in:Aktif,Tidak Aktif,Dipindahkan',
            'password'      => 'sometimes|string|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'jenis_kelamin.in' => 'Jenis kelamin harus Laki-laki atau Perempuan',
            'status_aktif.in'  => 'Status harus: Aktif, Tidak Aktif, atau Dipindahkan',
            'password.min'     => 'Password minimal 6 karakter',
        ];
    }
}
