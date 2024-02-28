<?php
declare(strict_types=1);

namespace ByteBuddyApi\Action\Birthday;

use ByteBuddyApi\Service\BirthdayService;
use DateTime;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BirthdayAction
{
    public function __construct(
        private readonly BirthdayService $birthdayService,
    )
    {
    }

    public function handleGetBirthdaysFromGuildAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $guildId = (int)$request->getQueryParams()['guildId'] ?? null;
        $result = $this->birthdayService->getBirthdays($guildId);
        $response->getBody()->write($result->getResponseAsJson());

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', '*')
            ->withHeader('Access-Control-Max-Age', '86400')
            ->withStatus($result->getStatusCode());
    }

    public function handleSetOrUpdateBirthdaysAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $guildId = (int)$request->getQueryParams()['guildId'] ?? null;
        $parsedBody = $request->getParsedBody();
        $birthdayDate = DateTime::createFromFormat('d.m.Y', $parsedBody['birthdayDate']);
        $result = $this->birthdayService->setOrUpdateBirthday($guildId, $parsedBody['userId'], $birthdayDate);
        $response->getBody()->write($result->getResponseAsJson());

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', '*')
            ->withHeader('Access-Control-Max-Age', '86400')
            ->withStatus($result->getStatusCode());
    }
}