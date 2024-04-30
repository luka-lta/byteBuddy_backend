<?php

declare(strict_types=1);

namespace ByteBuddyApi\Service\Results;

use ByteBuddyApi\Repository\UserRepository;

class AccessService
{
    public function __construct(
        private readonly JwtService $jwtService,
        private readonly UserRepository $userRepository,
    ) {
    }

    private function isAdministrator(int $userId): bool
    {
        $user = $this->userRepository->findUserById($userId);
        return $user->getRole() === 'ADMIN';
    }

    public function hasAccess(int $ownerId, string $token): bool
    {
        $tokenData = $this->jwtService->getUserDataFromToken($token);

        if ($this->isAdministrator($tokenData['uid'])) {
            return true;
        }

        if ($ownerId !== $tokenData['uid']) {
            return false;
        }

        return true;
    }
}
