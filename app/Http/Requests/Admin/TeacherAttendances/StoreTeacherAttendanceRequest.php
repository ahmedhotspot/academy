<?php

namespace App\Http\Requests\Admin\TeacherAttendances;

use App\Http\Requests\Admin\AdminRequest;
use App\Models\TeacherAttendance;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

class StoreTeacherAttendanceRequest extends AdminRequest
{
    private function teacherRule(): Exists
    {
        $user = auth()->user();

        return Rule::exists('users', 'id')->where(function ($query) use ($user) {
            $query->whereIn('id', function ($subQuery) {
                $subQuery->select('model_id')
                    ->from('model_has_roles')
                    ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                    ->where('model_has_roles.model_type', User::class)
                    ->where('roles.name', 'المعلم');
            });

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

