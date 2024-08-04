<?php

declare(strict_types=1);

namespace ByteBuddyApi\V2\Exception;

use Exception;

class ByteBuddyException extends Exception
{
    public function __construct(
        string    $message,
        int $code,
        private readonly mixed     $additionalData = null,
        private readonly ?Exception $previousException = null,
    ) {
        parent::__construct($message, $code, $this->previousException);
    }

    public function getExceptionData(): array
    {
        return [
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'additionalData' => $this->getAdditionalData(),
            'previousException' => $this->getPreviousException(),
        ];
    }

    public function getAdditionalData(): mixed
    {
        return $this->additionalData;
    }

    public function getPreviousException(): ?Exception
    {
        return $this->previousException;
    }
}