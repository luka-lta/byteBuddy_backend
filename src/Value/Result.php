<?php

declare(strict_types=1);

namespace ByteBuddyApi\Value;

final class Result
{
    private function __construct(
        private readonly bool $success,
        private readonly string $message,
        private readonly mixed $data,
        private readonly int $statusCode,
    ) {
    }

    public static function from(bool $success, string $message, mixed $data, int $statusCode): self
    {
        return new self($success, $message, $data, $statusCode);
    }

    public function getResponseArray(): array
    {
        if ($this->data === null) {
            return [
                'success' => $this->success,
                'message' => $this->message,
                'statusCode' => $this->statusCode
            ];
        }

        return [
            'success' => $this->success,
            'message' => $this->message,
            'statusCode' => $this->statusCode,
            'data' => $this->data
        ];
    }

    public function getResponseAsJson(): string
    {
        return json_encode($this->getResponseArray());
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
