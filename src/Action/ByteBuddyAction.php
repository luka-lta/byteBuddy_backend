<?php

declare(strict_types=1);

namespace ByteBuddyApi\Action;

use ByteBuddyApi\Value\Result;
use Psr\Http\Message\ResponseInterface;

abstract class ByteBuddyAction
{
    protected function buildResponse(ResponseInterface $response, Result $resultObject): ResponseInterface
    {
        $response->getBody()->write($resultObject->getResponseAsJson());
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', '*')
            ->withHeader('Access-Control-Max-Age', '86400')
            ->withStatus($resultObject->getStatusCode());
    }
}
