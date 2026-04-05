<?php

namespace App\Services\Admin;

use App\Models\Branch;
use App\Models\Group;
use App\Models\TeacherAttendance;
use App\Models\TeacherPayroll;
use App\Enums\UserStatusEnum;
use App\Models\User;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserManagementService extends BaseService
{
    public function getRoleOptions(): array
    {
        return Role::query()
            ->whereIn('name', ['المشرف العام', 'السكرتيرة', 'المعلم'])
            ->orderBy('id')
            ->pluck('name', 'name')
            ->toArray();
    }

    public function getStatusOptions(): array
    {
        return UserStatusEnum::options();
    }

    public function getBranchOptions(): array
    {
        return Branch::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function datatable(Request $request): array
    {
        $draw = (int) $request->input('draw', 1);
        $start = max((int) $request->input('start', 0), 0);
        $length = max((int) $request->input('length', 10), 1);
        $search = trim((string) data_get($request->input('search'), 'value', ''));

        $baseQuery = User::query()->with(['roles', 'branch:id,name'])->select([
            'id', 'name', 'phone', 'email', 'branch_id', 'status', 'created_at',
        ]);

        $recordsTotal = User::query()->count();

        if ($search !== '') {
            $baseQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $rows = $baseQuery
            ->orderByDesc('id')
            ->skip($start)
            ->take($length)
            ->get();

        $data = $rows->map(function (User $user) {
            $status = $user->status?->label() ?? 'غير محدد';
            $roleName = $user->roles->first()?->name ?? 'بدون دور';

            return [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email ?? '-',
                'branch' => $user->branch?->name ?? 'بدون فرع',
                'role' => $roleName,
                'status' => $status,
                'created_at' => optional($user->created_at)->format('Y-m-d'),
            ];
        })->values()->all();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function getUserProfile(User $user): array
    {
        $user->load(['roles', 'branch:id,name']);

        $roleNames = $user->roles->pluck('name')->values()->all();
        $isTeacher = in_array('المعلم', $roleNames, true);

        $teachingGroups = [];
        $attendanceSummary = [
            'total' => 0,
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'excused' => 0,
        ];
        $payrollSummary = [
            'count' => 0,
            'last_final' => '-',
            'last_month' => '-',
            'last_status' => '-',
            'last_status_badge' => 'bg-secondary',
        ];

        if ($isTeacher) {
            $groups = Group::query()
                ->with(['branch:id,name', 'studyLevel:id,name', 'studyTrack:id,name'])
                ->withCount('studentEnrollments')
                ->where('teacher_id', $user->id)
                ->latest('id')
                ->limit(8)
                ->get();

            $teachingGroups = $groups->map(fn (Group $group) => [
                'id' => $group->id,
                'name' => $group->name,
                'branch' => $group->branch?->name ?? '-',
                'level' => $group->studyLevel?->name ?? '-',
                'track' => $group->studyTrack?->name ?? '-',
                'students_count' => $group->student_enrollments_count,
                'status' => $group->status_label,
                'status_badge' => $group->status_badge_class,
            ])->values()->all();

            $attendances = TeacherAttendance::query()->where('teacher_id', $user->id)->get();
            $attendanceSummary = [
                'total' => $attendances->count(),
                'present' => $attendances->where('status', 'حاضر')->count(),
                'absent' => $attendances->where('status', 'غائب')->count(),
                'late' => $attendances->where('status', 'متأخر')->count(),
                'excused' => $attendances->where('status', 'بعذر')->count(),
            ];

            $lastPayroll = TeacherPayroll::query()
                ->where('teacher_id', $user->id)
                ->orderByDesc('year')
                ->orderByDesc('month')
                ->first();

            if ($lastPayroll) {
                $payrollSummary = [
                    'count' => TeacherPayroll::query()->where('teacher_id', $user->id)->count(),
                    'last_final' => $lastPayroll->formatted_final,
                    'last_month' => $lastPayroll->month_year,
                    'last_status' => $lastPayroll->status,
                    'last_status_badge' => $lastPayroll->status_badge_class,
                ];
            }
        }

        $notificationsCount = $user->notifications()->count();
        $unreadNotificationsCount = $user->notifications()->where('is_read', false)->count();
        $latestNotifications = $user->notifications()
            ->latest('created_at')
            ->limit(6)
            ->get()
            ->map(fn ($notification) => [
                'title' => $notification->title,
                'type' => $notification->type,
                'type_color' => $notification->type_color,
                'date' => optional($notification->created_at)->format('Y-m-d H:i'),
                'is_read' => $notification->is_read,
            ])
            ->values()
            ->all();

        return [
            'info' => [
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email ?? '-',
                'username' => $user->username ?? '-',
                'branch' => $user->branch?->name ?? 'بدون فرع',
                'status' => $user->status?->label() ?? '-',
                'status_value' => $user->status?->value ?? 'inactive',
                'roles' => $roleNames,
                'last_login' => optional($user->last_login_at)->format('Y-m-d H:i') ?? '-',
                'created_at' => optional($user->created_at)->format('Y-m-d H:i'),
                'updated_at' => optional($user->updated_at)->format('Y-m-d H:i'),
            ],
            'stats' => [
                'teaching_groups_count' => count($teachingGroups),
                'attendance_total' => $attendanceSummary['total'],
                'payroll_count' => $payrollSummary['count'],
                'notifications_count' => $notificationsCount,
                'unread_notifications_count' => $unreadNotificationsCount,
            ],
            'teaching_groups' => $teachingGroups,
            'attendance' => $attendanceSummary,
            'payroll' => $payrollSummary,
            'notifications' => $latestNotifications,
            'generated_at' => Carbon::now()->format('Y-m-d H:i'),
        ];
    }
}

