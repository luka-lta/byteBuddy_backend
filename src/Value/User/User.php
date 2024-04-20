<?php

declare(strict_types=1);

namespace ByteBuddyApi\Value\User;

use DateTime;
use Exception;

class User
{
    private function __construct(
        private readonly ?int    $userId,
        private readonly string $username,
        private readonly string $email,
        private ?string          $hashedPassword,
        private readonly array  $roles,
        private readonly ?DateTime $createdAt,
        private readonly ?DateTime $updatedAt,
    ) {
    }

    public static function from(
        ?int $userId,
        string $username,
        string $email,
        ?string $hashedPassword,
        array $roles,
        ?DateTime $createdAt = null,
        ?DateTime $updatedAt = null,
    ): self {
        return new self(
            $userId,
            $username,
            $email,
            $hashedPassword,
            $roles,
            $updatedAt,
            $createdAt,
        );
    }

    /**
     * @throws Exception
     */
    public static function fromDatabase(array $row): self
    {
        $createdAt = $row['created_at'] ? new DateTime($row['created_at']) : null;
        $updatedAt = $row['updated_at'] ? new DateTime($row['updated_at']) : null;
        return new self(
            $row['user_id'],
            $row['username'],
            $row['email'],
            $row['hashed_password'],
            explode(',', $row['roles']),
            $createdAt,
            $updatedAt,
            );
    }

    public function generatePasswordFromPlain(string $password): void
    {
        $this->hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->hashedPassword);
    }

    public function toArray(): array
    {
        return [
            'userId' => $this->userId,
            'username' => $this->username,
            'email' => $this->email,
            'roles' => $this->roles,
            'createdAt' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getHashedPassword(): string
    {
        return $this->hashedPassword;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }
}
