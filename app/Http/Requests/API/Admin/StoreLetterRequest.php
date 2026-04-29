<?php

namespace App\Http\Requests\API\Admin;

use App\Services\LetterTemplateService;
use Illuminate\Foundation\Http\FormRequest;

class StoreLetterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $validTypes = implode(',', array_keys(LetterTemplateService::getLetterTypes()));
        $type = $this->input('letter_type');

        $rules = [
            'letter_type'   => "required|in:{$validTypes}",
            'tanggal_surat' => 'required|date',
            'keterangan'    => 'nullable|string',
        ];

        switch ($type) {
            case 'surat_tugas_pelayanan':
                $rules['member_id']       = 'required|exists:members,id';
                $rules['tgl_mulai_tugas'] = 'required|date';
                $rules['tgl_akhir_tugas'] = 'required|date|after_or_equal:tgl_mulai_tugas';
                $rules['tujuan_tugas']    = 'required|string|min:10';
                break;

            case 'surat_pengantar':
                $rules['member_id']   = 'required|exists:members,id';
                $rules['keterangan']  = 'required|string|min:10';
                break;

            case 'surat_keterangan_jemaat_aktif':
                $rules['member_id']       = 'required|exists:members,id';
                $rules['tahun_bergabung'] = 'required|integer|min:1900|max:' . date('Y');
                break;

            case 'surat_nilai_sekolah':
                $rules['member_id']   = 'required|exists:members,id';
                $rules['asal_sekolah'] = 'required|string|min:3';
                $rules['kelas']        = 'required|string|min:1';
                $rules['semester']     = 'required|string|min:1';
                $rules['nilai']        = 'nullable|integer|min:0|max:100';
                break;

            case 'surat_pengajuan_baptisan':
                $rules['member_id'] = 'required|exists:members,id';
                break;

            case 'surat_pengajuan_penyerahan_anak':
                $rules['member_id']          = 'required|exists:members,id';
                $rules['nama_ayah']          = 'required|string|min:3';
                $rules['nama_ibu']           = 'required|string|min:3';
                $rules['nama_anak']          = 'required|string|min:1';
                $rules['tempat_lahir_anak']  = 'required|string|min:3';
                $rules['tanggal_lahir_anak'] = 'required|date';
                break;

            case 'surat_pengajuan_pernikahan':
                $rules['member_pria_id']     = 'required|exists:members,id';
                $rules['member_wanita_id']   = 'required|exists:members,id|different:member_pria_id';
                $rules['tanggal_pernikahan'] = 'required|date|after_or_equal:tanggal_surat';
                break;

            default:
                $rules['member_id'] = 'required|exists:members,id';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'letter_type.in'              => 'Tipe surat tidak valid',
            'member_id.exists'            => 'Jemaat tidak ditemukan',
            'member_pria_id.exists'       => 'Jemaat pria tidak ditemukan',
            'member_wanita_id.exists'     => 'Jemaat wanita tidak ditemukan',
            'member_wanita_id.different'  => 'Mempelai pria dan wanita tidak boleh sama',
            'tgl_akhir_tugas.after_or_equal' => 'Tanggal akhir harus setelah tanggal mulai',
            'tanggal_pernikahan.after_or_equal' => 'Tanggal pernikahan harus setelah tanggal surat',
        ];
    }
}
