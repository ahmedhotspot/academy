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
            'الاسم الكامل',
            'العمر',
            'الجنسية',
            'رقم الهوية',
            'الهاتف',
            'الواتساب',
            'الفرع',
            'الحالة',
        ];
    }

    public function map($student): array
    {
        return [
            $student->full_name,
            $student->age,
            $student->nationality,
            $student->identity_number,
            $student->phone,
            $student->whatsapp,
            $student->branch?->name ?? 'غير محدد',
            $student->status,
        ];
    }
}

