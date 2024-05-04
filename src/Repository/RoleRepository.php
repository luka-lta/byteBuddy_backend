<?php

declare(strict_types=1);

namespace ByteBuddyApi\Repository;

use ByteBuddyApi\Exception\ByteBuddyDatabaseException;
use ByteBuddyApi\Exception\ByteBuddyInvalidRoleException;
use ByteBuddyApi\Exception\ByteBuddyUserNotFoundException;
use ByteBuddyApi\Exception\ByteBuddyValidationException;
use ByteBuddyApi\Value\User\Role;
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
     * @throws ByteBuddyInvalidRoleException
     * @throws ByteBuddyValidationException
     * @throws ByteBuddyDatabaseException
     * @throws ByteBuddyUserNotFoundException
     */
    public function getRoleFromUserId(int $userId): Role
    {
        $this->userRepository->findUserById($userId);

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
        $this->userRepository->findUserById($userId);

        $sql = <<<SQL
            UPDATE users
            SET role = :role
            WHERE user_id = :userId
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'role' => $role->getRole(),
                'userId' => $userId,
            ]);
        } catch (PDOException $e) {
            throw new ByteBuddyDatabaseException('Failed to update role from user', 0, $e);
        }
    }
}
