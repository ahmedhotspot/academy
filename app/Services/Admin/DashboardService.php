<?php

namespace App\Services\Admin;

use App\Actions\Admin\Dashboard\LoadDashboardStatsAction;
use App\Services\BaseService;

class DashboardService extends BaseService
{
    public function __construct(private readonly LoadDashboardStatsAction $loadDashboardStatsAction)
    {
    }

    public function getStats(): array
    {
        return $this->loadDashboardStatsAction->handle();
    }
}

