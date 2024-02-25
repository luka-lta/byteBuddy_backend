<?php
declare(strict_types=1);

namespace ByteBuddyApi\Action\Config;

use ByteBuddyApi\Service\ConfigService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ConfigAction
{
    public function __construct(
        private readonly ConfigService $configService
    ) {}

    public function handleRegisterGuild(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $guildId = (int)$request->getQueryParams()['guildId'] ?? null;
        $parsedBody = $request->getParsedBody();

        $result = $this->configService->registerGuild($guildId, $parsedBody['serverName']);
        $response->getBody()->write($result->getResponseAsJson());

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', '*')
            ->withHeader('Access-Control-Max-Age', '86400')
            ->withStatus($result->getStatusCode());
    }

    public function handleGetConfigAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $guildId = (int)$request->getQueryParams()['guildId'] ?? null;
        $result = $this->configService->getConfigData($guildId);
        $response->getBody()->write($result->getResponseAsJson());

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', '*')
            ->withHeader('Access-Control-Max-Age', '86400')
            ->withStatus($result->getStatusCode());
    }

    public function handleSetConfigData(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $guildId = (int)$request->getQueryParams()['guildId'] ?? null;
        $parsedBody = $request->getParsedBody();

        $result = $this->configService->setConfigValue($guildId, $parsedBody);
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