<?php

namespace App\Actions\Admin\StudentSubscriptions;

use App\Actions\BaseAction;
use App\Models\StudentSubscription;

class DeleteStudentSubscriptionAction extends BaseAction
{
    public function handle(array $data): bool
    {
        /** @var StudentSubscription $subscription */
        $subscription = $data['subscription'];

        return (bool) $subscription->delete();
    }
}

