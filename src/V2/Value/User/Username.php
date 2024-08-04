<?php

declare(strict_types=1);

namespace ByteBuddyApi\V2\Value\User;

use ByteBuddyApi\V1\Exception\ByteBuddyValidationException;

class Username
{
    /**
     * @throws ByteBuddyValidationException
     */
    public function __construct(
        private readonly string $username,
    ) {
        if (empty($this->username)) {
            throw new ByteBuddyValidationException('Username cannot be empty', 400);
        }

        if (strlen($this->username) < 3) {
            throw new ByteBuddyValidationException('Username must be at least 3 characters long', 400);
        }

        if (strlen($this->username) > 20) {
            throw new ByteBuddyValidationException('Username must be at most 20 characters long', 400);
        }

        if (!preg_match('/^[a-zA-Z0-9_.]+$/', $this->username)) {
            throw new ByteBuddyValidationException(
                'Username can only contain letters, numbers, points, and underscores',
                400
            );
        }
    }

    /**
     * @throws ByteBuddyValidationException
     */
    public static function from(string $email): self
    {
        return new self($email);
    }

    public function getValue(): string
    {
        return $this->username;
    }
}
