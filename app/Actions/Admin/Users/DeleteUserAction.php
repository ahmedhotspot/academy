<?php

namespace App\Actions\Admin\Users;

use App\Actions\BaseAction;
use App\Models\User;

class DeleteUserAction extends BaseAction
{
    public function handle(array $data): bool
    {
        /** @var User $user */
        $user = $data['user'];

        if ($user->id === auth()->id()) {
            return false;
        }

        return (bool) $user->delete();
    }
}

