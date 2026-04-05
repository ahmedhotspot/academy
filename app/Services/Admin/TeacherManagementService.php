<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Http\Request;

class TeacherManagementService
{
    public function __construct(private readonly UserManagementService $userManagementService)
    {
    }

    public function datatable(Request $request): array
    {
        $draw = (int) $request->input('draw', 1);
        $start = max((int) $request->input('start', 0), 0);
        $length = max((int) $request->input('length', 10), 1);
        $search = trim((string) data_get($request->input('search'), 'value', ''));

        $baseQuery = User::query()
            ->role('المعلم')
            ->with(['branch:id,name'])
            ->select(['id', 'name', 'phone', 'email', 'branch_id', 'status', 'created_at']);

        if ($search !== '') {
            $baseQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $baseQuery)->count();
        $recordsTotal = User::query()->role('المعلم')->count();

        $rows = $baseQuery->orderByDesc('id')->skip($start)->take($length)->get();

        $data = $rows->map(fn (User $teacher) => [
            'id' => $teacher->id,
            'name' => $teacher->name,
            'phone' => $teacher->phone,
            'email' => $teacher->email ?? '-',
            'branch' => $teacher->branch?->name ?? 'بدون فرع',
            'status' => $teacher->status?->label() ?? 'غير محدد',
            'created_at' => optional($teacher->created_at)->format('Y-m-d'),
        ])->values()->all();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function findTeacherOrFail(User $teacher): User
    {
        abort_unless($teacher->hasRole('المعلم'), 404);

        return $teacher->load(['roles', 'branch']);
    }

    public function getTeacherProfile(User $teacher): array
    {
        return $this->userManagementService->getUserProfile($this->findTeacherOrFail($teacher));
    }

    public function getBranchOptions(): array
    {
        return $this->userManagementService->getBranchOptions();
    }

    public function getStatusOptions(): array
    {
        return $this->userManagementService->getStatusOptions();
    }
}

