<?php

declare(strict_types=1);

namespace ByteBuddyApi\Value\User;

use ByteBuddyApi\Exception\ByteBuddyValidationException;
use DateTime;
use Exception;

class User
{
    private function __construct(
        private readonly ?int $userId,
        private Username $username,
        private Email $email,
        private ?Password $hashedPassword,
        private Role $role,
        private readonly ?DateTime $createdAt,
        private readonly ?DateTime $updatedAt,
        private bool $disabled = false,
    ) {
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
            Username::from($username),
            Email::from($email),
            Password::fromHash($hashedPassword),
            Role::from($role),
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
            Username::from($row['username']),
            Email::from($row['email']),
            Password::fromHash($row['hashed_password']),
            Role::from($row['role']),
            $createdAt,
            $updatedAt,
            filter_var($row['disabled'], FILTER_VALIDATE_BOOL),
        );
    }

    public function toArray(): array
    {
        return [
            'userId' => $this->userId,
            'username' => $this->username->getValue(),
            'email' => $this->email->getValue(),
            'role' => $this->role->getValue(),
            'disabled' => $this->disabled,
            'createdAt' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getUsername(): Username
    {
        return $this->username;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): Password
    {
        return $this->hashedPassword;
    }

    public function getRole(): Role
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

    public function setUsername(Username $username): void
    {
        $this->username = $username;
    }

    public function setEmail(Email $email): void
    {
        $this->email = $email;
    }

    public function setHashedPassword(?string $hashedPassword): void
    {
        $this->hashedPassword = $hashedPassword;
    }

    public function setRole(Role $role): void
    {
        $this->role = $role;
    }

    public function setDisabled(bool $disabled): void
    {
        $this->disabled = $disabled;
    }
}
