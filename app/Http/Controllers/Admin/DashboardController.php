<?php

namespace App\Http\Controllers\Admin;

use App\Services\Dashboard\DashboardStatsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends AdminController
{
    protected string $title = 'لوحة التحكم';

    public function __construct(private readonly DashboardStatsService $dashboardStatsService)
    {
    }

    public function index(Request $request): View
    {
        $monthsRange = (int) $request->query('months', 6);

        return $this->adminView(
            'admin.dashboard.index',
            $this->dashboardStatsService->buildDashboardData(auth()->user(), $monthsRange)
        );
    }
}
