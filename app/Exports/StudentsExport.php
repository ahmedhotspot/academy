<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Student::with('branch')->get();
    }

    public function headings(): array
    {
        return [
            'كود الطالب',
            'الاسم الكامل',
            'العمر',
            'الجنسية',
            'رقم الهوية',
            'تاريخ انتهاء الهوية',
            'الهاتف',
            'الواتساب',
            'الفرع',
            'الحالة',
        ];
    }

    public function map($student): array
    {
        return [
            $student->student_code,
            $student->full_name,
            $student->age,
            $student->nationality,
            $student->identity_number,
            optional($student->identity_expiry_date)->format('Y-m-d'),
            $student->phone,
            $student->whatsapp,
            $student->branch?->name ?? 'غير محدد',
            $student->status,
        ];
    }
}

