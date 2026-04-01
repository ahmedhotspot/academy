<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\Admin\StudentManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private StudentManagementService $service) {}

    public function index(Request $request): View
    {
        $student = Auth::guard('student')->user();
        $profile = $this->service->getStudentProfile($student);

        return view('student.dashboard', compact('student', 'profile'));
    }
}

