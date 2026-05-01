<?php

namespace App\Http\Requests\API\Admin;

use App\Services\LetterTemplateService;
use Illuminate\Foundation\Http\FormRequest;

// StoreLetterRequest menangani validasi input saat admin create surat baru
class StoreLetterRequest extends FormRequest
{
    // Hanya admin yang sudah login yang bisa mengakses (dijaga oleh middleware role:admin)
    public function authorize(): bool
    {
        return true;
    }

    // API menangani validasi dinamis berdasarkan tipe surat yang dipilih
    public function rules(): array
    {
        // Ambil semua slug tipe surat yang valid dari service
        $validTypes = implode(',', array_keys(LetterTemplateService::getLetterTypes()));
        $type = $this->input('letter_type');

        // Validasi dasar yang berlaku untuk semua tipe surat
        $rules = [
            'letter_type'   => "required|in:{$validTypes}",
            'tanggal_surat' => 'required|date',
            'keterangan'    => 'nullable|string',
        ];

        // Tambahkan validasi tambahan sesuai tipe surat yang dipilih
        switch ($type) {

            // API menangani validasi surat tugas pelayanan
            case 'surat_tugas_pelayanan':
                $rules['member_id']       = 'required|exists:members,id';
                $rules['tgl_mulai_tugas'] = 'required|date';
                $rules['tgl_akhir_tugas'] = 'required|date|after_or_equal:tgl_mulai_tugas';
                $rules['tujuan_tugas']    = 'required|string|min:10';
                break;

            // API menangani validasi surat pengantar
            case 'surat_pengantar':
                $rules['member_id']  = 'required|exists:members,id';
                $rules['keterangan'] = 'required|string|min:10';
                break;

            // API menangani validasi surat keterangan jemaat aktif
            case 'surat_keterangan_jemaat_aktif':
                $rules['member_id']       = 'required|exists:members,id';
                $rules['tahun_bergabung'] = 'required|integer|min:1900|max:' . date('Y');
                break;

            // API menangani validasi surat nilai sekolah
            case 'surat_nilai_sekolah':
                $rules['member_id']    = 'required|exists:members,id';
                $rules['asal_sekolah'] = 'required|string|min:3';
                $rules['kelas']        = 'required|string|min:1';
                $rules['semester']     = 'required|string|min:1';
                $rules['nilai']        = 'nullable|integer|min:0|max:100';
                break;

            // API menangani validasi surat pengajuan baptisan
            case 'surat_pengajuan_baptisan':
                $rules['member_id'] = 'required|exists:members,id';
                break;

            // API menangani validasi surat pengajuan penyerahan anak
            case 'surat_pengajuan_penyerahan_anak':
                $rules['member_id']          = 'required|exists:members,id';
                $rules['nama_ayah']          = 'required|string|min:3';
                $rules['nama_ibu']           = 'required|string|min:3';
                $rules['nama_anak']          = 'required|string|min:1';
                $rules['tempat_lahir_anak']  = 'required|string|min:3';
                $rules['tanggal_lahir_anak'] = 'required|date';
                break;

            // API menangani validasi surat pengajuan pernikahan (butuh dua jemaat berbeda)
            case 'surat_pengajuan_pernikahan':
                $rules['member_pria_id']     = 'required|exists:members,id';
                $rules['member_wanita_id']   = 'required|exists:members,id|different:member_pria_id';
                $rules['tanggal_pernikahan'] = 'required|date|after_or_equal:tanggal_surat';
                break;

            // Default fallback jika tipe tidak dikenali
            default:
                $rules['member_id'] = 'required|exists:members,id';
        }

        return $rules;
    }

    // Pesan error dalam Bahasa Indonesia
    public function messages(): array
    {
        return [
            'letter_type.in'                    => 'Tipe surat tidak valid',
            'member_id.exists'                  => 'Jemaat tidak ditemukan',
            'member_pria_id.exists'             => 'Jemaat pria tidak ditemukan',
            'member_wanita_id.exists'           => 'Jemaat wanita tidak ditemukan',
            'member_wanita_id.different'        => 'Mempelai pria dan wanita tidak boleh sama',
            'tgl_akhir_tugas.after_or_equal'    => 'Tanggal akhir harus setelah tanggal mulai',
            'tanggal_pernikahan.after_or_equal' => 'Tanggal pernikahan harus setelah tanggal surat',
        ];
    }
}
