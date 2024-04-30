<?php

declare(strict_types=1);

namespace ByteBuddyApi\Repository;

use ByteBuddyApi\Exception\ByteBuddyDatabaseException;
use ByteBuddyApi\Exception\ByteBuddyUserAlreadyExistsException;
use ByteBuddyApi\Exception\ByteBuddyUserNotFoundException;
use ByteBuddyApi\Value\User\User;
use Exception;
use PDO;
use PDOException;

class UserRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @throws Exception
     */
    public function createUser(User $user): User
    {
        if ($this->userExists($user->getEmail())) {
            throw new ByteBuddyUserAlreadyExistsException('User with this email already exists', 409);
        }

        $sql = <<<SQL
            INSERT INTO 
                users (username, email, hashed_password, role)
            VALUES 
                (:username, :email, :hashedPassword, :roles)
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'hashedPassword' => $user->getHashedPassword(),
                'role' => $user->getRole()
            ]);
            $lastId = $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new ByteBuddyDatabaseException('Failed to create user', 500, $e);
        }

        return User::from(
            (int)$lastId,
            $user->getUsername(),
            $user->getEmail(),
            $user->getHashedPassword(),
            $user->getRole(),
        );
    }

    /**
     * @throws ByteBuddyDatabaseException
     * @throws Exception
     */
    public function updateUser(User $user): void
    {
        $this->findUserById($user->getUserId());

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
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'userId' => $user->getUserId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'role' => $user->getRole()
            ]);
        } catch (PDOException $e) {
            throw new ByteBuddyDatabaseException('Failed to update user', 500, $e);
        }
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function changePassword(int $userId, string $plainPassword): void
    {
        $user = $this->findUserById($userId);
        $user->generatePasswordFromPlain($plainPassword);

        $sql = <<<SQL
            UPDATE 
                users
            SET 
                hashed_password = :hashedPassword
            WHERE 
                user_id = :userId
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'userId' => $userId,
                'hashedPassword' => $user->getHashedPassword()
            ]);
        } catch (PDOException $e) {
            throw new ByteBuddyDatabaseException('Failed to change password', 500, $e);
        }
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function deleteUser(int $userId): void
    {
        $this->findUserById($userId);

        $sql = <<<SQL
            DELETE FROM 
                users
            WHERE 
                user_id = :userId
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'userId' => $userId
            ]);
        } catch (PDOException $e) {
            throw new ByteBuddyDatabaseException('Failed to delete user', 500, $e);
        }
    }

    /**
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
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $userData = $stmt->fetchAll();
        } catch (PDOException) {
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
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'email' => $email
            ]);

            $count = $stmt->fetchColumn();
        } catch (PDOException) {
            throw new ByteBuddyDatabaseException('Failed to check if user exists', 500);
        }

        return $count > 0;
    }

    /**
     * @throws Exception
     */
    public function findUserByEmail(string $email): User
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
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'email' => $email
            ]);

            $user = $stmt->fetch();
        } catch (PDOException) {
            throw new ByteBuddyDatabaseException('Failed to find user by email', 500);
        }

        if ($user === false) {
            throw new ByteBuddyUserNotFoundException('User not found', 404);
        }

        return User::fromDatabase($user);
    }

    /**
     * @throws ByteBuddyDatabaseException
     * @throws Exception
     */
    public function findUserById(int $userId): ?User
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
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'userId' => $userId
            ]);

            $user = $stmt->fetch();
        } catch (PDOException) {
            throw new ByteBuddyDatabaseException('Failed to find user by id', 500);
        }

        if ($user === false) {
            throw new ByteBuddyUserNotFoundException('User not found', 404);
        }

        return User::fromDatabase($user);
    }
}
