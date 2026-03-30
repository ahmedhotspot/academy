<?php

namespace App\Http\Requests\Admin\GroupSchedules;

use App\Http\Requests\Admin\AdminRequest;
use Illuminate\Validation\Rule;

class StoreGroupScheduleRequest extends AdminRequest
{
    public function rules(): array
    {
        return [
            'group_id' => ['required', 'integer', 'exists:groups,id'],
            'day_name' => ['required', Rule::in(['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'])],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'group_id' => 'الحلقة',
            'day_name' => 'اليوم',
            'start_time' => 'وقت البداية',
            'end_time' => 'وقت النهاية',
            'status' => 'الحالة',
        ];
    }
}

