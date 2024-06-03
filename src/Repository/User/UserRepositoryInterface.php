<?php


namespace ByteBuddyApi\Repository\User;

use ByteBuddyApi\Value\User\Password;
use ByteBuddyApi\Value\User\User;

interface UserRepositoryInterface
{
    public function create(User $user): User;
    public function update(User $user): bool;
    public function find(int $userId): ?array;
    public function delete(int $userId): void;
    public function changePassword(int $userId, Password $hashedPassword): void;
}
