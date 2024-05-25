<?php

declare(strict_types=1);

namespace ByteBuddyApi\Value\User;

use ByteBuddyApi\Exception\ByteBuddyValidationException;

class Password
{
    public function __construct(
        private string $hashedPassword,
    ) {
    }

    /**
     * @throws ByteBuddyValidationException
     */
    public static function fromPlainText(string $plainPassword): self
    {
        if ($plainPassword === '') {
            throw new ByteBuddyValidationException('Password cannot be empty', 400);
        }

        if (strlen($plainPassword) < 8) {
            throw new ByteBuddyValidationException('Password must be at least 8 characters long', 400);
        }

        if (!preg_match('/[A-Z]/', $plainPassword)) {
            throw new ByteBuddyValidationException('Password must contain at least one uppercase letter', 400);
        }

        $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

        return new self($hashedPassword);
    }

    public static function fromHash(string $hashedPassword): self
    {
        return new self($hashedPassword);
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
        if ($plainPassword === '') {
            throw new ByteBuddyValidationException('Password cannot be empty', 400);
        }

        if (strlen($plainPassword) < 8) {
            throw new ByteBuddyValidationException('Password must be at least 8 characters long', 400);
        }

        if (!preg_match('/[A-Z]/', $plainPassword)) {
            throw new ByteBuddyValidationException('Password must contain at least one uppercase letter', 400);
        }

        $this->hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);
    }
}
