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
            'full_name'       => $row['الاسم_الكامل'] ?? $row['الاسم'] ?? null,
            'age'             => $row['العمر'] ?? null,
            'nationality'     => $row['الجنسية'] ?? null,
            'identity_number' => $row['رقم_الهوية'] ?? null,
            'phone'           => $row['الهاتف'] ?? null,
            'whatsapp'        => $row['الواتساب'] ?? null,
            'branch_id'       => $branchId,
            'status'          => 'active',
        ]);
    }

    public function rules(): array
    {
        return [
            'الاسم_الكامل' => 'required|string|max:255',
            'العمر'        => 'required|integer|min:3',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'الاسم_الكامل.required' => 'حقل الاسم الكامل مطلوب',
            'العمر.required'        => 'حقل العمر مطلوب',
            'العمر.integer'         => 'يجب أن يكون العمر رقماً صحيحاً',
        ];
    }
}

