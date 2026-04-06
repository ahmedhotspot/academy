<?php

namespace App\Http\Requests\Admin\TeacherAttendances;

use App\Http\Requests\Admin\AdminRequest;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

class UpdateTeacherAttendanceRequest extends AdminRequest
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
        return [
            'teacher_id' => ['required', 'integer', $this->teacherRule()],
            'attendance_date' => ['required', 'date'],
            'status' => ['required', Rule::in(['حاضر', 'غائب', 'متأخر', 'بعذر'])],
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
        ];
    }
}

