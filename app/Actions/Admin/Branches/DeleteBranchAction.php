<?php

namespace App\Actions\Admin\Branches;

use App\Actions\BaseAction;
use App\Models\Branch;

class DeleteBranchAction extends BaseAction
{
    public function handle(array $data): bool
    {
        /** @var Branch $branch */
        $branch = $data['branch'];

        $hasRelations = $branch->users()->exists()
            || $branch->students()->exists()
            || $branch->groups()->exists();

        if ($hasRelations) {
            return false;
        }

        return (bool) $branch->delete();
    }
}
