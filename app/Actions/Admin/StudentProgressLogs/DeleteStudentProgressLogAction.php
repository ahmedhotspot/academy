<?php

namespace App\Actions\Admin\StudentProgressLogs;

use App\Actions\BaseAction;
use App\Models\StudentProgressLog;

class DeleteStudentProgressLogAction extends BaseAction
{
    public function handle(array $data): bool
    {
        /** @var StudentProgressLog $log */
        $log = $data['progressLog'];

        return (bool) $log->delete();
    }
}

