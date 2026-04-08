<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): ?Student
    {
        $branchId = null;
        if (!empty($row['الفرع'])) {
            $branch = Branch::where('name', $row['الفرع'])->first();
            $branchId = $branch?->id;
        }

        return new Student([
            'student_code'     => $row['كود_الطالب'] ?? $row['كود'] ?? null,
            'full_name'       => $row['الاسم_الكامل'] ?? $row['الاسم'] ?? null,
            'age'             => $row['العمر'] ?? null,
            'nationality'     => $row['الجنسية'] ?? null,
            'identity_number' => $row['رقم_الهوية'] ?? null,
            'identity_expiry_date' => $row['تاريخ_انتهاء_الهوية'] ?? null,
            'phone'           => $row['الهاتف'] ?? null,
            'whatsapp'        => $row['الواتساب'] ?? null,
            'branch_id'       => $branchId,
            'status'          => 'active',
        ]);
    }

    public function rules(): array
    {
        return [
            'كود_الطالب'   => 'required|string|max:50',
            'الاسم_الكامل' => 'required|string|max:255',
            'العمر'        => 'required|integer|min:3',
            'رقم_الهوية'   => 'required|string|max:100',
            'تاريخ_انتهاء_الهوية' => 'required|date',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'كود_الطالب.required'   => 'حقل كود الطالب مطلوب',
            'الاسم_الكامل.required' => 'حقل الاسم الكامل مطلوب',
            'العمر.required'        => 'حقل العمر مطلوب',
            'العمر.integer'         => 'يجب أن يكون العمر رقماً صحيحاً',
            'رقم_الهوية.required'   => 'حقل رقم الهوية مطلوب',
            'تاريخ_انتهاء_الهوية.required' => 'حقل تاريخ انتهاء الهوية مطلوب',
            'تاريخ_انتهاء_الهوية.date' => 'تاريخ انتهاء الهوية يجب أن يكون تاريخًا صحيحًا',
        ];
    }
}

