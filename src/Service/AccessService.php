<?php

declare(strict_types=1);

namespace ByteBuddyApi\Service;

use ByteBuddyApi\Exception\ByteBuddyDatabaseException;
use ByteBuddyApi\Exception\ByteBuddyUserNotFoundException;
use ByteBuddyApi\Exception\ByteBuddyValidationException;

class AccessService
{
    public function __construct(
        private readonly UserService $userService,
    ) {
    }

    /**
     * @throws ByteBuddyValidationException
     * @throws ByteBuddyUserNotFoundException
     * @throws ByteBuddyDatabaseException
     */
    private function isAdministrator(int $accessUserId): bool
    {
        $user = $this->userService->getUserById($accessUserId);
        return $user->getRole() === 'ADMIN';
    }

    /**
     * @throws ByteBuddyValidationException
     * @throws ByteBuddyUserNotFoundException
     * @throws ByteBuddyDatabaseException
     */
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
