<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\UpdateMemberBiodataRequest;
use App\Http\Resources\LetterResource;
use App\Http\Resources\MemberResource;
use App\Models\Letter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

// MemberApiController menangani aksi jemaat terhadap data miliknya sendiri
class MemberApiController extends BaseController
{
    // API menangani tampil profil jemaat yang sedang login
    public function show(Request $request): JsonResponse
    {
        return $this->success(MemberResource::make($request->user()));
    }

    // API menangani update biodata jemaat (nama, alamat, no telepon)
    public function update(UpdateMemberBiodataRequest $request): JsonResponse
    {
        $request->user()->update($request->validated());

        // Reload data terbaru dari database setelah update
        return $this->success(MemberResource::make($request->user()->fresh()), 'Biodata berhasil diperbarui');
    }

    // API menangani daftar surat milik jemaat yang sedang login dengan filter keyword
    public function letters(Request $request): JsonResponse
    {
        $member  = $request->user();
        $keyword = $request->query('keyword');

        // Filter surat berdasarkan tipe atau nomor surat jika ada keyword
        $letters = Letter::where('member_id', $member->id)
            ->when($keyword, fn ($q) => $q->where(function ($q) use ($keyword) {
                $q->where('tipe_surat', 'LIKE', "%{$keyword}%")
                  ->orWhere('nomor_surat', 'LIKE', "%{$keyword}%");
            }))
            ->orderByDesc('created_at')
            ->get();

        return $this->success(LetterResource::collection($letters));
    }

    // API menangani download PDF surat milik jemaat (hanya surat yang sudah tersimpan di disk)
    public function downloadLetter(Request $request, int $letterId): BinaryFileResponse|JsonResponse
    {
        // Pastikan surat ini benar milik jemaat yang login
        $letter = Letter::where('id', $letterId)
            ->where('member_id', $request->user()->id)
            ->first();

        if (!$letter) {
            return $this->error('Surat tidak ditemukan', 404);
        }

        // Cek apakah file PDF sudah tersimpan di storage
        $path = $letter->pdf_path ? storage_path('app/' . $letter->pdf_path) : null;

        if (!$path || !file_exists($path)) {
            return $this->error('File PDF belum tersedia', 404);
        }

        // Buat nama file yang rapi dari nomor surat
        $filename = 'surat_' . str_replace('/', '-', $letter->nomor_surat) . '.pdf';
        return response()->download($path, $filename);
    }
}
