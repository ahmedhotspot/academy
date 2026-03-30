<?php

namespace App\Actions\Admin\Branches;

use App\Actions\BaseAction;
use App\Models\Branch;

class UpdateBranchAction extends BaseAction
{
    public function handle(array $data): Branch
    {
        /** @var Branch $branch */
        $branch = $data['branch'];
        unset($data['branch']);

        $branch->update($data);

        return $branch;
    }
}

