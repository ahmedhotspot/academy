<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Services\Admin\GuardianManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private GuardianManagementService $service) {}

    public function index(Request $request): View
    {
        $guardian = Auth::guard('guardian')->user();
        $profile  = $this->service->getGuardianProfile($guardian);

        return view('guardian.dashboard', compact('guardian', 'profile'));
    }
}

