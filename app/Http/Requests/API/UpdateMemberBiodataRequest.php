<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberBiodataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_lengkap' => 'sometimes|string|max:255',
            'alamat'       => 'sometimes|string|max:500',
            'no_telepon'   => 'sometimes|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_lengkap.max' => 'Nama lengkap maksimal 255 karakter',
            'no_telepon.max'   => 'Nomor telepon maksimal 20 karakter',
        ];
    }
}
