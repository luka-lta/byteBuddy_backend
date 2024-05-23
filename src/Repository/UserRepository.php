<?php

declare(strict_types=1);

namespace ByteBuddyApi\Repository;

use ByteBuddyApi\Exception\ByteBuddyDatabaseException;
use ByteBuddyApi\Exception\ByteBuddyValidationException;
use ByteBuddyApi\Utils\PdoUtil;
use ByteBuddyApi\Value\User\User;

class UserRepository
{
    public function __construct(
        private readonly PdoUtil $pdo,
    ) {
    }

    /**
     * @throws ByteBuddyValidationException
     * @throws ByteBuddyDatabaseException
     */
    public function createUser(User $user): User
    {
        $sql = <<<SQL
            INSERT INTO 
                users (username, email, hashed_password)
            VALUES 
                (:username, :email, :hashedPassword)
        SQL;

        try {
            $this->pdo->execute($sql, [
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'hashedPassword' => $user->getHashedPassword(),
            ]);
            $lastId = $this->pdo->getLastInsertedId();
        } catch (ByteBuddyDatabaseException $e) {
            throw new ByteBuddyDatabaseException(
                'Failed to create user',
                500,
                $user->toArray(),
                $e
            );
        }

        return User::from(
            $lastId,
            $user->getUsername(),
            $user->getEmail(),
            $user->getHashedPassword(),
            $user->getRole(),
        );
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function updateUser(User $user): void
    {
        $sql = <<<SQL
            UPDATE 
                users
            SET 
                username = :username,
                email = :email,
                role = :role
            WHERE 
                user_id = :userId
        SQL;

        try {
            $this->pdo->executeUpdate($sql, [
                'userId' => $user->getUserId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'role' => $user->getRole()
            ]);
        } catch (ByteBuddyDatabaseException $e) {
            throw new ByteBuddyDatabaseException('Failed to update user', 500, $e);
        }
    }


    /**
     * @throws ByteBuddyDatabaseException
     */
    public function changePassword(int $userId, string $hashedPassword): void
    {
        $sql = <<<SQL
            UPDATE 
                users
            SET 
                hashed_password = :hashedPassword
            WHERE 
                user_id = :userId
        SQL;

        try {
            $this->pdo->execute($sql, [
                'userId' => $userId,
                'hashedPassword' => $hashedPassword
            ]);
        } catch (ByteBuddyDatabaseException $e) {
            throw new ByteBuddyDatabaseException('Failed to change password', 500, $e);
        }
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function deleteUser(int $userId): void
    {
        $sql = <<<SQL
            DELETE FROM 
                users
            WHERE 
                user_id = :userId
        SQL;

        try {
            $this->pdo->execute($sql, [
                'userId' => $userId
            ]);
        } catch (ByteBuddyDatabaseException $e) {
            throw new ByteBuddyDatabaseException('Failed to delete user', 500, $e);
        }
    }

    /**
     * @throws ByteBuddyValidationException
     * @throws ByteBuddyDatabaseException
     */
    public function getAllUsers(): array
    {
        $sql = <<<SQL
            SELECT
                *
            FROM 
                users
        SQL;

        try {
            $userData = $this->pdo->fetchAllQuery($sql);
        } catch (ByteBuddyDatabaseException) {
            throw new ByteBuddyDatabaseException('Failed to get all users', 500);
        }

        $users = [];
        foreach ($userData as $user) {
            $userObject = User::fromDatabase($user);
            $users[] = $userObject->toArray();
        }

        return $users;
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function userExists(string $email): bool
    {
        $sql = <<<SQL
            SELECT 
                COUNT(*)
            FROM 
                users
            WHERE 
                email = :email
        SQL;

        try {
            $count = $this->pdo->fetchColumn($sql, [
                'email' => $email
            ]);
        } catch (ByteBuddyDatabaseException $e) {
            throw new ByteBuddyDatabaseException('Failed to check if user exists', 500, [
                'email' => $email
            ], $e);
        }

        return $count > 0;
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function userExistsById(int $userId): bool
    {
        $sql = <<<SQL
            SELECT 
                COUNT(*)
            FROM 
                users
            WHERE 
                user_id = :userId
        SQL;

        try {
            $count = $this->pdo->fetchColumn($sql, [
                'userId' => $userId
            ]);
        } catch (ByteBuddyDatabaseException $e) {
            throw new ByteBuddyDatabaseException(
                'Failed to check if user exists',
                500,
                [
                    'email' => $userId
                ],
                $e
            );
        }

        return $count > 0;
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function getUserByEmail(string $email): array
    {
        $sql = <<<SQL
            SELECT
                *
            FROM 
                users
            WHERE 
                email = :email
        SQL;

        try {
            $result = $this->pdo->fetchQuery($sql, [
                'email' => $email
            ]);

            return $result ?: [];
        } catch (ByteBuddyDatabaseException $e) {
            throw new ByteBuddyDatabaseException('Failed to find user by email', 500, [
                'email' => $email
            ], $e);
        }
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function getUserById(int $userId): array
    {
        $sql = <<<SQL
            SELECT
                *
            FROM 
                users
            WHERE 
                user_id = :userId
        SQL;

        try {
            $result = $this->pdo->fetchQuery($sql, [
                'userId' => $userId
            ]);

            return $result ?: [];
        } catch (ByteBuddyDatabaseException $e) {
            throw new ByteBuddyDatabaseException(
                'Failed to find user by id',
                500,
                [
                    'userId' => $userId
                ],
                $e
            );
        }
    }
}
