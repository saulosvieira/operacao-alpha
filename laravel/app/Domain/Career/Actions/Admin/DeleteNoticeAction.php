<?php

declare(strict_types=1);

namespace App\Domain\Career\Actions\Admin;

use App\Domain\Career\Models\Notice;

final class DeleteNoticeAction
{
    /**
     * Execute the action to delete a notice
     *
     * @param int $noticeId
     * @return void
     */
    public function execute(int $noticeId): void
    {
        $notice = Notice::findOrFail($noticeId);
        $notice->delete();
    }
}
