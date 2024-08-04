<?php

declare(strict_types=1);

namespace ByteBuddyApi\V1\Service;

use ByteBuddyApi\V1\Exception\ByteBuddyDatabaseException;
use ByteBuddyApi\V1\Exception\ByteBuddyUserNotFoundException;
use ByteBuddyApi\V1\Exception\ByteBuddyValidationException;

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
        return $user->getRole()->getValue() === 'ADMIN';
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
