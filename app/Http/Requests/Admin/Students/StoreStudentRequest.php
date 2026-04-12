<?php

namespace App\Http\Requests\Admin\Students;

use App\Http\Requests\Admin\AdminRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends AdminRequest
{
    public function rules(): array
    {
        $guardianExistsRule = Rule::exists('guardians', 'id');
        $guardianBranchId = $this->guardianBranchId();

        if ($guardianBranchId) {
            $guardianExistsRule = $guardianExistsRule->where(fn ($query) => $query->where('branch_id', $guardianBranchId));
        }

        return [
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'student_code' => ['required', 'string', 'max:50', 'unique:students,student_code'],
            'guardian_mode' => ['nullable', Rule::in(['none', 'existing', 'new'])],
            'guardian_id' => ['nullable', 'integer', 'required_if:guardian_mode,existing', $guardianExistsRule],
            'guardian_full_name' => ['nullable', 'string', 'max:255', 'required_if:guardian_mode,new'],
            'guardian_phone' => ['nullable', 'string', 'max:20', 'required_if:guardian_mode,new'],
            'guardian_whatsapp' => ['nullable', 'string', 'max:20'],
            'full_name' => ['required', 'string', 'max:255'],
            'enrollment_date' => ['required', 'date'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'age' => ['required', 'integer', 'min:5', 'max:100'],
            'nationality' => ['required', 'string', 'max:100'],
            'identity_number' => ['required', 'string', 'max:100'],
            'identity_expiry_date' => ['nullable', 'date'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'residency_number' => ['nullable', 'string', 'max:100', 'required_with:residency_expiry_date'],
            'residency_expiry_date' => ['nullable', 'date', 'required_with:residency_number'],
            'phone' => ['required', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'branch_id' => 'الفرع',
            'student_code' => 'كود الطالب',
            'guardian_mode' => 'طريقة اختيار ولي الأمر',
            'guardian_id' => 'ولي الأمر',
            'guardian_full_name' => 'اسم ولي الأمر الجديد',
            'guardian_phone' => 'هاتف ولي الأمر الجديد',
            'guardian_whatsapp' => 'واتساب ولي الأمر الجديد',
            'full_name' => 'الاسم الكامل',
            'enrollment_date' => 'تاريخ الالتحاق',
            'birth_date' => 'تاريخ الميلاد',
            'age' => 'العمر',
            'nationality' => 'الجنسية',
            'identity_number' => 'رقم الهوية أو الجواز',
            'identity_expiry_date' => 'تاريخ انتهاء الهوية أو الجواز',
            'gender' => 'الجنس',
            'residency_number' => 'رقم الإقامة',
            'residency_expiry_date' => 'تاريخ انتهاء الإقامة',
            'phone' => 'رقم الهاتف',
            'whatsapp' => 'رقم الواتساب',
            'status' => 'الحالة',
        ];
    }

    private function guardianBranchId(): ?int
    {
        $user = $this->user();

        if ($user && ! $user->isSuperAdmin() && $user->branch_id) {
            return (int) $user->branch_id;
        }

        $branchId = $this->input('branch_id');

        return $branchId ? (int) $branchId : null;
    }
}

