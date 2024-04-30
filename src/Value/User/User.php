<?php

declare(strict_types=1);

namespace ByteBuddyApi\Value\User;

use ByteBuddyApi\Exception\ByteBuddyValidationException;
use DateTime;
use Exception;

class User
{
    /**
     * @throws ByteBuddyValidationException
     */
    private function __construct(
        private readonly ?int      $userId,
        private readonly string    $username,
        private readonly string    $email,
        private ?string            $hashedPassword,
        private readonly string     $role,
        private readonly ?DateTime $createdAt,
        private readonly ?DateTime $updatedAt,
    ) {
        if ($this->username === '') {
            throw new ByteBuddyValidationException('Username cannot be empty');
        }

        if ($this->email === '') {
            throw new ByteBuddyValidationException('Email cannot be empty');
        }
    }

    public static function from(
        ?int $userId,
        string $username,
        string $email,
        ?string $hashedPassword,
        string $role,
        ?DateTime $createdAt = null,
        ?DateTime $updatedAt = null,
    ): self {
        return new self(
            $userId,
            $username,
            $email,
            $hashedPassword,
            $role,
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
            $row['role'],
            $createdAt,
            $updatedAt,
            );
    }

    /**
     * @throws ByteBuddyValidationException
     */
    public function generatePasswordFromPlain(string $password): void
    {
        if ($password === '') {
            throw new ByteBuddyValidationException('Password cannot be empty');
        }

        if (strlen($password) < 8) {
            throw new ByteBuddyValidationException('Password must be at least 8 characters long');
        }

        if (!preg_match('/[A-Z]/', $password)) {
            throw new ByteBuddyValidationException('Password must contain at least one uppercase letter');
        }

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
            'role' => $this->role,
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

    public function getRole(): string
    {
        return $this->role;
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
