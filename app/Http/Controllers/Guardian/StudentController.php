<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\Admin\StudentManagementService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function __construct(private StudentManagementService $service) {}

    public function show(Student $student): View
    {
        $guardian = Auth::guard('guardian')->user();

        // التأكد أن الطالب تابع لولي الأمر الحالي فقط
        abort_if($student->guardian_id !== $guardian->id, 403, 'ليس لديك صلاحية الوصول.');

        $profile = $this->service->getStudentProfile($student);

        return view('guardian.students.show', compact('student', 'profile', 'guardian'));
    }
}

