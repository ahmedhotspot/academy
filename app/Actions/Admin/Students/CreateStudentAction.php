<?php

namespace App\Actions\Admin\Students;

use App\Actions\BaseAction;
use App\Models\Guardian;
use App\Models\Student;

class CreateStudentAction extends BaseAction
{
    public function handle(array $data): Student
    {
        $guardianMode = $data['guardian_mode'] ?? 'none';

        if ($guardianMode === 'new') {
            $guardian = Guardian::query()->create([
                'branch_id' => $data['branch_id'],
                'full_name' => $data['guardian_full_name'],
                'phone' => $data['guardian_phone'],
                'whatsapp' => $data['guardian_whatsapp'] ?? null,
                'status' => 'active',
            ]);

            $data['guardian_id'] = $guardian->id;
        }

        if ($guardianMode === 'none') {
            $data['guardian_id'] = null;
        }

        unset(
            $data['guardian_mode'],
            $data['guardian_full_name'],
            $data['guardian_phone'],
            $data['guardian_whatsapp']
        );

        return Student::query()->create($data);
    }
}

