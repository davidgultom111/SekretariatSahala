<?php

namespace App\Http\Requests\API\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_lengkap'  => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'required|date|before:today',
            'tempat_lahir'  => 'required|string|max:255',
            'alamat'        => 'required|string|max:500',
            'no_telepon'    => 'required|string|max:20',
            'status_aktif'  => 'required|in:Aktif,Tidak Aktif,Dipindahkan',
        ];
    }

    public function messages(): array
    {
        return [
            'jenis_kelamin.in' => 'Jenis kelamin harus Laki-laki atau Perempuan',
            'status_aktif.in'  => 'Status harus: Aktif, Tidak Aktif, atau Dipindahkan',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini',
        ];
    }
}
