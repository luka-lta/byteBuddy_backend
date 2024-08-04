<?php

declare(strict_types=1);

namespace ByteBuddyApi\V2\Value\User;

use ByteBuddyApi\V1\Exception\ByteBuddyValidationException;

class Password
{
    public function __construct(
        private string $hashedPassword,
    ) {
    }

    public static function fromHash(string $hashedPassword): self
    {
        return new self($hashedPassword);
    }

    public static function fromPlain(string $plainPassword): self
    {
        return new self(password_hash($plainPassword, PASSWORD_BCRYPT));
    }

    public function verify(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->hashedPassword);
    }

    public function getValue(): string
    {
        return $this->hashedPassword;
    }

    /**
     * @throws ByteBuddyValidationException
     */
    public function generatePasswordFromPlain(string $plainPassword): void
    {
        self::validatePassword($plainPassword);
        $this->hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);
    }

    /**
     * @throws ByteBuddyValidationException
     */
    private static function validatePassword(string $plainPassword): void
    {
        if (empty($plainPassword)) {
            throw new ByteBuddyValidationException('Password cannot be empty', 400);
        }

        if (strlen($plainPassword) < 8) {
            throw new ByteBuddyValidationException('Password must be at least 8 characters long', 400);
        }

        if (!preg_match('/[A-Z]/', $plainPassword)) {
            throw new ByteBuddyValidationException('Password must contain at least one uppercase letter', 400);
        }
    }
}
