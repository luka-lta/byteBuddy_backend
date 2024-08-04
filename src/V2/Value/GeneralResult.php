<?php

declare(strict_types=1);

namespace ByteBuddyApi\V2\Value;

use ByteBuddyApi\V2\Slim\ResultInterface;
use Psr\Http\Message\ResponseInterface;

class GeneralResult
{
    private function __construct(
        private readonly ResultInterface $result,
        private readonly int $status,
    ) {
    }

    public static function from(
        ResultInterface $result,
        int $status,
    ): self {
        return new self($result, $status);
    }

    public function getResponse(ResponseInterface $response): ResponseInterface
    {
        $result = $this->result->toArray();
        $result = array_merge(['status' => $this->status], $result);

        $response->getBody()->write($result);

        return $response
            ->withStatus($this->status)
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', '*')
            ->withHeader('Access-Control-Max-Age', '86400');
    }
}
