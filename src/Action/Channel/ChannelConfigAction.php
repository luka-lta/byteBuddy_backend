<?php
declare(strict_types=1);

namespace ByteBuddyApi\Action\Channel;

use ByteBuddyApi\Service\ChannelConfigService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ChannelConfigAction
{
    public function __construct(
        private readonly ChannelConfigService $channelConfigService
    )
    {
    }


    public function handleGetChannels(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $guildId = (int)$request->getQueryParams()['guildId'] ?? null;
        $result = $this->channelConfigService->getAllChannels($guildId);
        $response->getBody()->write($result->getResponseAsJson());

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', '*')
            ->withHeader('Access-Control-Max-Age', '86400')
            ->withStatus($result->getStatusCode());
    }

    public function handleSetChannels(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $guildId = (int)$request->getQueryParams()['guildId'] ?? null;
        $channelType = $request->getQueryParams()['channelType'] ?? null;
        $parsedBody = $request->getParsedBody();

        $result = $this->channelConfigService->setChannel($guildId, $channelType, $parsedBody['channelId']);
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