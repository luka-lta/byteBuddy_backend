<?php

declare(strict_types=1);

namespace ByteBuddyApi\V2\Value\User;

use ByteBuddyApi\V1\Exception\ByteBuddyValidationException;

class Email
{
    /**
     * @throws ByteBuddyValidationException
     */
    public function __construct(
        private readonly string $email,
    ) {
        if ($this->email === '') {
            throw new ByteBuddyValidationException('Email cannot be empty', 400);
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new ByteBuddyValidationException('Email is not valid', 400);
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
        return $this->email;
    }
}
