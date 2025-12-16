<?php

namespace App\Domain\Notification\Actions;

use App\Domain\Notification\Repositories\NotificationRepository;

class UnsubscribeFromNotificationsAction
{
    public function __construct(
        private NotificationRepository $repository
    ) {}

    public function execute(string $endpoint): bool
    {
        return $this->repository->delete($endpoint);
    }

    public function executeByUser(string $userId): bool
    {
        return $this->repository->deleteByUser($userId);
    }
}
