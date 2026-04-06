<?php

namespace App\Http\Requests\Admin\TeacherAttendances;

use App\Http\Requests\Admin\AdminRequest;
use App\Models\TeacherAttendance;
use Illuminate\Validation\Rule;

class StoreTeacherAttendanceRequest extends AdminRequest
{
    private function teacherRule(): Rule
    {
        $user = auth()->user();

        return Rule::exists('users', 'id')->where(function ($query) use ($user) {
            $query->whereHas('roles', fn ($roleQuery) => $roleQuery->where('name', 'المعلم'));

            if ($user && ! $user->isSuperAdmin() && $user->branch_id) {
                $query->where('branch_id', $user->branch_id);
            }
        });
    }

    public function rules(): array
    {
        if ($this->has('entries')) {
            return [
                'attendance_date' => ['required', 'date'],
                'entries' => ['required', 'array', 'min:1'],
                'entries.*.teacher_id' => ['required', 'integer', $this->teacherRule(), 'distinct'],
                'entries.*.status' => ['required', Rule::in(TeacherAttendance::STATUSES)],
                'entries.*.notes' => ['nullable', 'string', 'max:1000'],
            ];
        }

        return [
            'teacher_id' => ['required', 'integer', $this->teacherRule()],
            'attendance_date' => ['required', 'date'],
            'status' => ['required', Rule::in(TeacherAttendance::STATUSES)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'teacher_id' => 'المعلم',
            'attendance_date' => 'تاريخ الحضور',
            'status' => 'الحالة',
            'notes' => 'الملاحظات',
            'entries' => 'كشف الحضور',
            'entries.*.teacher_id' => 'المعلم',
            'entries.*.status' => 'الحالة',
            'entries.*.notes' => 'الملاحظات',
        ];
    }
}

