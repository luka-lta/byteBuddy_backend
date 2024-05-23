<?php

declare(strict_types=1);

namespace ByteBuddyApi\Repository;

use ByteBuddyApi\Exception\ByteBuddyDatabaseException;
use ByteBuddyApi\Exception\ByteBuddyValidationException;
use ByteBuddyApi\Utils\PdoUtil;
use ByteBuddyApi\Value\User\Password;
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
                'username' => $user->getUsername()->getValue(),
                'email' => $user->getEmail()->getValue(),
                'hashedPassword' => $user->getPassword()->getValue(),
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
            $user->getUsername()->getValue(),
            $user->getEmail()->getValue(),
            $user->getPassword()->getValue(),
            $user->getRole()->getValue(),
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
                'username' => $user->getUsername()->getValue(),
                'email' => $user->getEmail()->getValue(),
                'role' => $user->getRole()->getValue()
            ]);
        } catch (ByteBuddyDatabaseException $e) {
            throw new ByteBuddyDatabaseException('Failed to update user', 500, $e);
        }
    }


    /**
     * @throws ByteBuddyDatabaseException
     */
    public function changePassword(int $userId, Password $hashedPassword): void
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
                'hashedPassword' => $hashedPassword->getValue()
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
        return $this->recordExists('email', $email);
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function userExistsById(int $userId): bool
    {
        return $this->recordExists('user_id', $userId);
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function getuserByUsername(string $username): array
    {
        return $this->getUserByField('username', $username);
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function getUserByEmail(string $email): array
    {
        return $this->getUserByField('email', $email);
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function getUserById(int $userId): array
    {
        return $this->getUserByField('user_id', $userId);
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function userNameExists(string $username): bool
    {
        return $this->recordExists('username', $username);
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    private function recordExists(string $field, mixed $value): bool
    {
        $sql = <<<SQL
            SELECT 
                COUNT(*)
            FROM 
                users
            WHERE 
                $field = :value
        SQL;

        try {
            $count = $this->pdo->fetchColumn($sql, [
                'value' => $value
            ]);
        } catch (ByteBuddyDatabaseException $e) {
            throw new ByteBuddyDatabaseException("Failed to check if $field exists", 500, [
                $field => $value
            ], $e);
        }

        return $count > 0;
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    private function getUserByField(string $field, mixed $value): array
    {
        $sql = <<<SQL
            SELECT
                *
            FROM 
                users
            WHERE 
                $field = :value
        SQL;

        try {
            $result = $this->pdo->fetchQuery($sql, [
                'value' => $value
            ]);

            return $result ?: [];
        } catch (ByteBuddyDatabaseException $e) {
            throw new ByteBuddyDatabaseException("Failed to find user by $field", 500, [
                $field => $value
            ], $e);
        }
    }
}
