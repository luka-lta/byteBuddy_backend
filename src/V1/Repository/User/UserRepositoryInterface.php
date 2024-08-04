<?php

namespace ByteBuddyApi\V1\Repository\User;

use ByteBuddyApi\V1\Value\User\Password;
use ByteBuddyApi\V1\Value\User\User;

interface UserRepositoryInterface
{
    public function create(User $user): User;
    public function update(User $user): bool;
    public function find(int $userId): ?array;
    public function findAll(): ?array;
    public function delete(int $userId): void;
    public function changePassword(int $userId, Password $hashedPassword): void;
}
