<?php

namespace App\Actions\Admin\Users;

use App\Actions\BaseAction;
use App\Models\User;

class CreateUserAction extends BaseAction
{
    public function handle(array $data): User
    {
        $role = $data['role'];
        unset($data['role']);

        if ($role === 'المشرف العام') {
            $data['branch_id'] = null;
        }

        $user = User::query()->create($data);
        $user->syncRoles([$role]);

        return $user;
    }
}

