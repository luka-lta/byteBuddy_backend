<?php
declare(strict_types=1);

namespace ByteBuddyApi\Action\Channel;

use ByteBuddyApi\Action\ByteBuddyAction;
use ByteBuddyApi\Service\ChannelConfigService;
use ByteBuddyApi\Value\Result;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ChannelConfigAction extends ByteBuddyAction
{
    public function __construct(
        private readonly ChannelConfigService $channelConfigService
    )
    {
    }

    public function handleGetChannels(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $guildId = $request->getQueryParams()['guildId'] ?? null;
        $channelType = $request->getQueryParams()['channelType'] ?? null;

        if (!$guildId) {
            $result = Result::from(false, 'Guild ID is required', null, 400);
            return $this->buildResponse($response, $result);
        }

        $result = $this->channelConfigService->getAllChannelsOrSpecific($guildId, $channelType);
        return $this->buildResponse($response, $result);
    }

    public function handleSetChannels(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $guildId = $request->getQueryParams()['guildId'] ?? null;
        $channelType = $request->getQueryParams()['channelType'] ?? null;
        $parsedBody = $request->getParsedBody();

        if (!$guildId) {
            $result = Result::from(false, 'Guild ID is required', null, 400);
            return $this->buildResponse($response, $result);
        }

        if (!$channelType) {
            $result = Result::from(false, 'Channel type is required', null, 400);
            return $this->buildResponse($response, $result);
        }

        $result = $this->channelConfigService->setChannel($guildId, $channelType, $parsedBody['channelId']);
        return $this->buildResponse($response, $result);
    }
}