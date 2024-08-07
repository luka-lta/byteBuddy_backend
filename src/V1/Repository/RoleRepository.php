<?php

declare(strict_types=1);

namespace ByteBuddyApi\V1\Repository;

use ByteBuddyApi\V1\Exception\ByteBuddyDatabaseException;
use ByteBuddyApi\V1\Exception\ByteBuddyUserNotFoundException;
use ByteBuddyApi\V1\Exception\ByteBuddyValidationException;
use ByteBuddyApi\V1\Repository\User\UserRepository;
use ByteBuddyApi\V1\Value\User\Role;
use PDO;
use PDOException;

class RoleRepository
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly UserRepository $userRepository
    ) {
    }

    /**
     * @throws ByteBuddyValidationException
     * @throws ByteBuddyDatabaseException
     */
    public function getRoleFromUserId(int $userId): Role
    {
        $this->userRepository->getUserById($userId);

        $sql = <<<SQL
            SELECT role
            FROM users
            WHERE user_id = :userId
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['userId' => $userId]);
            $role = $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new ByteBuddyDatabaseException('Failed to get role from user ID', 0, $e);
        }

        return Role::from($role);
    }


    /**
     * @throws ByteBuddyValidationException
     * @throws ByteBuddyUserNotFoundException
     * @throws ByteBuddyDatabaseException
     */
    public function updateRoleFromUser(int $userId, Role $role): void
    {
        $this->userRepository->getUserById($userId);

        $sql = <<<SQL
            UPDATE users
            SET role = :role
            WHERE user_id = :userId
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'role' => $role->getValue(),
                'userId' => $userId,
            ]);
        } catch (PDOException $e) {
            throw new ByteBuddyDatabaseException('Failed to update role from user', 0, $e);
        }
    }
}
