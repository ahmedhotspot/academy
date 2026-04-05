<?php

namespace App\Actions\Admin\Users;

use App\Actions\BaseAction;
use App\Models\User;

class UpdateUserAction extends BaseAction
{
    public function handle(array $data): User
    {
        /** @var User $user */
        $user = $data['user'];
        $role = $data['role'];

        unset($data['user'], $data['role']);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        if ($role === 'المشرف العام') {
            $data['branch_id'] = null;
        }

        $user->update($data);
        $user->syncRoles([$role]);

        return $user;
    }
}

