<?php

declare(strict_types=1);

namespace ByteBuddyApi\Value\User;

use ByteBuddyApi\Exception\ByteBuddyInvalidRoleException;

class Role
{
    public const ADMIN = 'ADMIN';
    public const USER = 'USER';

    private array $validRoles = [
        self::ADMIN,
        self::USER,
    ];

    /**
     * @throws ByteBuddyInvalidRoleException
     */
    private function __construct(
        private readonly string $role
    ) {
        if (!in_array($role, $this->validRoles)) {
            throw new ByteBuddyInvalidRoleException(
                'Invalid role. Please use: [' . implode(', ', $this->validRoles) . ']',
                400
            );
        }
    }

    /**
     * @throws ByteBuddyInvalidRoleException
     */
    public static function from(string $role): self
    {
        return new self($role);
    }

    public function toArray(): array
    {
        return [
            'role' => $this->role,
        ];
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public static function getValidRoles(): array
    {
        return static::getValidRoles();
    }
}
