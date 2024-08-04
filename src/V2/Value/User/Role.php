<?php

declare(strict_types=1);

namespace ByteBuddyApi\V2\Value\User;

use ByteBuddyApi\V1\Exception\ByteBuddyValidationException;

class Role
{
    public const ADMIN = 'ADMIN';
    public const USER = 'USER';

    private const VALID_ROLES = [
        self::ADMIN,
        self::USER,
    ];

    /**
     * @throws ByteBuddyValidationException
     */
    private function __construct(
        private readonly string $role
    ) {
        if (!in_array($role, self::VALID_ROLES, true)) {
            throw new ByteBuddyValidationException(
                'Invalid role. Please use: [' . implode(', ', self::VALID_ROLES) . ']',
                400
            );
        }
    }

    /**
     * @throws ByteBuddyValidationException
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

    public function getValue(): string
    {
        return $this->role;
    }

    public static function getValidRoles(): array
    {
        return self::VALID_ROLES;
    }
}
