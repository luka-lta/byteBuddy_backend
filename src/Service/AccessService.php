<?php

declare(strict_types=1);

namespace ByteBuddyApi\Service;

use ByteBuddyApi\Repository\UserRepository;

class AccessService
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    private function isAdministrator(int $accessUserId): bool
    {
        $user = $this->userRepository->findUserById($accessUserId);
        return $user->getRole() === 'ADMIN';
    }

    public function hasAccess(int $ownerId, int $accessUserId): bool
    {
        if ($this->isAdministrator($accessUserId)) {
            return true;
        }

        if ($ownerId !== $accessUserId) {
            return false;
        }

        return true;
    }
}
