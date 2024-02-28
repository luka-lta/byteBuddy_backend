<?php
declare(strict_types=1);

namespace ByteBuddyApi\Action\Guild;

use ByteBuddyApi\Action\ByteBuddyAction;
use ByteBuddyApi\Service\GuildService;
use ByteBuddyApi\Value\ResultObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GuildAction extends ByteBuddyAction
{
    public function __construct(
        private readonly GuildService $configService
    )
    {
    }

    public function handleRegisterGuild(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $guildId = $request->getQueryParams()['guildId'] ?? null;
        $parsedBody = $request->getParsedBody();

        if (!$guildId) {
            $result = ResultObject::from(false, 'Guild ID is required', null, 400);
            return $this->buildResponse($response, $result);
        }

        $result = $this->configService->registerGuild($guildId, $parsedBody['serverName']);
        $response->getBody()->write($result->getResponseAsJson());

        return $this->buildResponse($response, $result);
    }

    public function handleGetConfigAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $guildId = $request->getQueryParams()['guildId'] ?? null;

        if (!$guildId) {
            $result = ResultObject::from(false, 'Guild ID is required', null, 400);
            return $this->buildResponse($response, $result);
        }

        $result = $this->configService->getConfigData($guildId);
        $response->getBody()->write($result->getResponseAsJson());

        return $this->buildResponse($response, $result);
    }

    public function handleSetConfigData(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $guildId = $request->getQueryParams()['guildId'] ?? null;
        $parsedBody = $request->getParsedBody();

        if (!$guildId) {
            $result = ResultObject::from(false, 'Guild ID is required', null, 400);
            return $this->buildResponse($response, $result);
        }

        $result = $this->configService->setConfigValue($guildId, $parsedBody);
        $response->getBody()->write($result->getResponseAsJson());

        return $this->buildResponse($response, $result);
    }
}