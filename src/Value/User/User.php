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
        private readonly ?int $userId,
        private string $username,
        private string $email,
        private ?string $hashedPassword,
        private string $role,
        private readonly ?DateTime $createdAt,
        private readonly ?DateTime $updatedAt,
        private bool $disabled = false,
    ) {
        if ($this->username === '') {
            throw new ByteBuddyValidationException('Username cannot be empty', 400);
        }

        if ($this->email === '') {
            throw new ByteBuddyValidationException('Email cannot be empty', 400);
        }
    }

    /**
     * @throws ByteBuddyValidationException
     */
    public static function from(
        ?int $userId,
        string $username,
        string $email,
        ?string $hashedPassword,
        string $role,
        ?DateTime $createdAt = null,
        ?DateTime $updatedAt = null,
        bool $disabled = false,
    ): self {
        return new self(
            $userId,
            $username,
            $email,
            $hashedPassword,
            $role,
            $updatedAt,
            $createdAt,
            $disabled,
        );
    }

    /**
     * @throws ByteBuddyValidationException
     */
    public static function fromDatabase(array $row): self
    {
        try {
            $createdAt = $row['created_at'] ? new DateTime($row['created_at']) : null;
            $updatedAt = $row['updated_at'] ? new DateTime($row['updated_at']) : null;
        } catch (Exception $e) {
            throw new ByteBuddyValidationException('Invalid date format', 500, $row, $e);
        }

        return new self(
            $row['user_id'],
            $row['username'],
            $row['email'],
            $row['hashed_password'],
            $row['role'],
            $createdAt,
            $updatedAt,
            filter_var($row['disabled'], FILTER_VALIDATE_BOOL),
        );
    }

    /**
     * @throws ByteBuddyValidationException
     */
    public function generatePasswordFromPlain(string $password): void
    {
        if ($password === '') {
            throw new ByteBuddyValidationException('Password cannot be empty', 400);
        }

        if (strlen($password) < 8) {
            throw new ByteBuddyValidationException('Password must be at least 8 characters long', 400);
        }

        if (!preg_match('/[A-Z]/', $password)) {
            throw new ByteBuddyValidationException('Password must contain at least one uppercase letter', 400);
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

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setHashedPassword(?string $hashedPassword): void
    {
        $this->hashedPassword = $hashedPassword;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    public function setDisabled(bool $disabled): void
    {
        $this->disabled = $disabled;
    }
}
