<?php

declare(strict_types=1);

namespace App\Domain\Career\Actions\Admin;

use App\Domain\Career\Models\Career;
use Exception;

final class DeleteCareerAction
{
    /**
     * Execute the action to delete a career
     *
     * @param int $careerId
     * @return void
     * @throws Exception
     */
    public function execute(int $careerId): void
    {
        $career = Career::findOrFail($careerId);

        if ($career->exams()->count() > 0) {
            throw new Exception('Cannot delete career with associated exams.');
        }

        $career->delete();
    }
}
