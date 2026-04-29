<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\UpdateMemberBiodataRequest;
use App\Http\Resources\LetterResource;
use App\Http\Resources\MemberResource;
use App\Models\Letter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MemberApiController extends BaseController
{
    public function show(Request $request): JsonResponse
    {
        return $this->success(MemberResource::make($request->user()));
    }

    public function update(UpdateMemberBiodataRequest $request): JsonResponse
    {
        $request->user()->update($request->validated());
        return $this->success(MemberResource::make($request->user()->fresh()), 'Biodata berhasil diperbarui');
    }

    public function letters(Request $request): JsonResponse
    {
        $member  = $request->user();
        $keyword = $request->query('keyword');

        $letters = Letter::where('member_id', $member->id)
            ->when($keyword, fn ($q) => $q->where(function ($q) use ($keyword) {
                $q->where('tipe_surat', 'LIKE', "%{$keyword}%")
                  ->orWhere('nomor_surat', 'LIKE', "%{$keyword}%");
            }))
            ->orderByDesc('created_at')
            ->get();

        return $this->success(LetterResource::collection($letters));
    }

    public function downloadLetter(Request $request, int $letterId): BinaryFileResponse|JsonResponse
    {
        $letter = Letter::where('id', $letterId)
            ->where('member_id', $request->user()->id)
            ->first();

        if (!$letter) {
            return $this->error('Surat tidak ditemukan', 404);
        }

        $path = $letter->pdf_path ? storage_path('app/' . $letter->pdf_path) : null;

        if (!$path || !file_exists($path)) {
            return $this->error('File PDF belum tersedia', 404);
        }

        $filename = 'surat_' . str_replace('/', '-', $letter->nomor_surat) . '.pdf';
        return response()->download($path, $filename);
    }
}
