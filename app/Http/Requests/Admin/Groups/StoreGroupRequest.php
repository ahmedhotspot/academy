<?php

namespace App\Http\Requests\Admin\Groups;

use App\Http\Requests\Admin\AdminRequest;
use App\Models\User;
use Illuminate\Validation\Rule;

class StoreGroupRequest extends AdminRequest
{
    private function teacherRules(): array
    {
        $user = $this->user();
        $branchId = $user && ! $user->isSuperAdmin() && $user->branch_id
            ? (int) $user->branch_id
            : (int) $this->input('branch_id');

        return [
            'required',
            'integer',
            function (string $attribute, mixed $value, \Closure $fail) use ($branchId) {
                $query = User::query()
                    ->role('المعلم')
                    ->whereKey($value);

                if ($branchId > 0) {
                    $query->where('branch_id', $branchId);
                }

                if (! $query->exists()) {
                    $fail('المعلم المختار غير متاح لهذا الفرع.');
                }
            },
        ];
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'teacher_id' => $this->teacherRules(),
            'study_level_id' => ['required', 'integer', 'exists:study_levels,id'],
            'study_track_id' => ['required', 'integer', 'exists:study_tracks,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['individual', 'group'])],
            'schedule_type' => ['required', Rule::in(['daily', 'weekly'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'branch_id' => 'الفرع',
            'teacher_id' => 'المعلم',
            'study_level_id' => 'المستوى',
            'study_track_id' => 'المسار',
            'name' => 'اسم الحلقة',
            'type' => 'نوع الحلقة',
            'schedule_type' => 'نظام الحلقة',
            'status' => 'الحالة',
        ];
    }
}

