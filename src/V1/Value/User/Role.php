<?php

declare(strict_types=1);

namespace ByteBuddyApi\V1\Value\User;

use ByteBuddyApi\V1\Exception\ByteBuddyValidationException;

class Role
{
    public const ADMIN = 'ADMIN';
    public const USER = 'USER';

    private array $validRoles = [
        self::ADMIN,
        self::USER,
    ];

    /**
     * @throws ByteBuddyValidationException
     */
    private function __construct(
        private readonly string $role
    ) {
        if (!in_array($role, $this->validRoles)) {
            throw new ByteBuddyValidationException(
                'Invalid role. Please use: [' . implode(', ', $this->validRoles) . ']',
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
        return static::getValidRoles();
    }
}
