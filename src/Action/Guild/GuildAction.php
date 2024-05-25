<?php

declare(strict_types=1);

namespace ByteBuddyApi\Action\Guild;

use ByteBuddyApi\Action\ByteBuddyAction;
use ByteBuddyApi\Service\Results\GuildService;
use ByteBuddyApi\Value\Result;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GuildAction extends ByteBuddyAction
{
    public function __construct(
        private readonly GuildService $configService
    ) {
    }

    public function handleGetAllGuilds(ResponseInterface $response): ResponseInterface
    {
        $result = $this->configService->getAllGuilds();
        return $this->buildResponse($response, $result);
    }

    public function handleRegisterGuild(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $guildId = $request->getQueryParams()['guildId'] ?? null;
        $parsedBody = $request->getParsedBody();

        if (!$guildId) {
            $result = Result::from(false, 'Guild ID is required', null, 400);
            return $this->buildResponse($response, $result);
        }

        $result = $this->configService->registerGuild($guildId, $parsedBody['serverName']);
        return $this->buildResponse($response, $result);
    }

    public function handleGetConfigAction(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $guildId = $request->getQueryParams()['guildId'] ?? null;

        if (!$guildId) {
            $result = Result::from(false, 'Guild ID is required', null, 400);
            return $this->buildResponse($response, $result);
        }

        $result = $this->configService->getConfigData($guildId);
        return $this->buildResponse($response, $result);
    }

    public function handleSetConfigData(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $guildId = $request->getQueryParams()['guildId'] ?? null;
        $parsedBody = $request->getParsedBody();

        if (!$guildId) {
            $result = Result::from(false, 'Guild ID is required', null, 400);
            return $this->buildResponse($response, $result);
        }

        $result = $this->configService->setConfigValue($guildId, $parsedBody);
        return $this->buildResponse($response, $result);
    }
}
