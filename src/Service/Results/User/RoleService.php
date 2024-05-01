<?php

declare(strict_types=1);

namespace ByteBuddyApi\Service\Results\User;

use ByteBuddyApi\Exception\ByteBuddyException;
use ByteBuddyApi\Repository\RoleRepository;
use ByteBuddyApi\Service\ValidationService;
use ByteBuddyApi\Value\Result;
use ByteBuddyApi\Value\User\Role;

class RoleService
{
    public function __construct(
        private readonly RoleRepository $roleRepository,
    ) {
    }

    public function getRoleFromUser(int $userId): Result
    {
        try {
            $role = $this->roleRepository->getRoleFromUserId($userId);
        } catch (ByteBuddyException $e) {
            return Result::from(false, $e->getMessage(), null, $e->getCode());
        }

        return Result::from(true, 'Role found', $role->toArray(), 200);
    }

    public function updateRoleFromUser(int $userId, string $role): Result
    {
        try {
            $role = Role::from($role);
            $this->roleRepository->updateRoleFromUser($userId, $role);
        } catch (ByteBuddyException $e) {
            return Result::from(false, $e->getMessage(), null, $e->getCode());
        }

        return Result::from(true, 'Role updated', null, 200);
    }
}
