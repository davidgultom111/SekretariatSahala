<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\API\Admin\StoreMemberRequest;
use App\Http\Requests\API\Admin\UpdateMemberRequest;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemberController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $members = Member::when($request->query('search'), fn ($q, $s) =>
                $q->where('nama_lengkap', 'LIKE', "%{$s}%")
                  ->orWhere('id_jemaat', 'LIKE', "%{$s}%")
            )
            ->when($request->query('status'), fn ($q, $s) => $q->where('status_aktif', $s))
            ->orderBy('nama_lengkap')
            ->paginate($request->query('per_page', 15));

        return $this->success(MemberResource::collection($members)->response()->getData(true));
    }

    public function store(StoreMemberRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make('12345');

        $member = Member::create($data);

        return $this->created(MemberResource::make($member), 'Data jemaat berhasil ditambahkan');
    }

    public function show(Member $member): JsonResponse
    {
        return $this->success(MemberResource::make($member));
    }

    public function update(UpdateMemberRequest $request, Member $member): JsonResponse
    {
        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $member->update($data);

        return $this->success(MemberResource::make($member->fresh()), 'Data jemaat berhasil diperbarui');
    }

    public function destroy(Member $member): JsonResponse
    {
        $member->forceDelete();
        return $this->noContent();
    }
}
