<?php

declare(strict_types=1);

namespace ByteBuddyApi\Service\Results\User;

use ByteBuddyApi\Exception\ByteBuddyException;
use ByteBuddyApi\Repository\RoleRepository;
use ByteBuddyApi\Service\AccessService;
use ByteBuddyApi\Value\Result;
use ByteBuddyApi\Value\User\Role;

class RoleService
{
    public function __construct(
        private readonly RoleRepository $roleRepository,
        private readonly AccessService  $accessService,
    ) {
    }

    public function getRoleFromUser(int $userId, int $accessUserId): Result
    {
        try {
            if (!$this->accessService->hasAccess($userId, $accessUserId)) {
                return Result::from(false, 'Forbidden', null, 403);
            }
            $role = $this->roleRepository->getRoleFromUserId($userId);
        } catch (ByteBuddyException $e) {
            return Result::from(false, $e->getMessage(), null, $e->getCode());
        }

        return Result::from(true, 'Role found', $role->toArray(), 200);
    }

    public function updateRoleFromUser(int $userId, string $role, int $accessUserId): Result
    {
        try {
            if (!$this->accessService->hasAccess($userId, $accessUserId)) {
                return Result::from(false, 'Forbidden', null, 403);
            }
            $role = Role::from($role);
            $this->roleRepository->updateRoleFromUser($userId, $role);
        } catch (ByteBuddyException $e) {
            return Result::from(false, $e->getMessage(), null, $e->getCode());
        }

        return Result::from(true, 'Role updated', null, 200);
    }
}
