<?php

declare(strict_types=1);

namespace ByteBuddyApi\V1\Service\Results;

use ByteBuddyApi\V1\Exception\ByteBuddyException;
use ByteBuddyApi\V1\Repository\ChannelConfigRepository;
use ByteBuddyApi\V1\Value\Channel;
use ByteBuddyApi\V1\Value\Result;
use Exception;

class ChannelConfigService
{
    public function __construct(private readonly ChannelConfigRepository $channelConfigRepository)
    {
    }

    public function getAllChannelsOrSpecific(string $guildId, string|null $channelType): Result
    {
        try {
            if ($channelType) {
                $channel = $this->channelConfigRepository->getChannel($guildId, $channelType);
                return Result::from(true, 'Channel fetched successfully', $channel->asArray(), 200);
            }

            $channels = $this->channelConfigRepository->getAllChannels($guildId);
            $channelArray = [];
/** @var Channel $channel */
            foreach ($channels as $channel) {
                $channelArray[] = $channel->asArray();
            }

            return Result::from(true, 'Channels fetched successfully', $channelArray, 200);
        } catch (ByteBuddyException $exception) {
            return Result::from(false, $exception->getMessage(), null, $exception->getCode());
        } catch (Exception) {
            return Result::from(false, 'An error occurred', null, 500);
        }
    }

    public function setChannel(string $guildId, string $channelType, string $channelId): Result
    {
        if ($guildId == null) {
            return Result::from(false, 'GuildId must be set', null, 400);
        }

        if ($channelType == null) {
            return Result::from(false, 'Channel type must be set', null, 400);
        }

        try {
            $channel = Channel::from($channelId, $channelType);
            $this->channelConfigRepository->setChannel($guildId, $channel, $channelType);
        } catch (ByteBuddyException $exception) {
            return Result::from(false, $exception->getMessage(), null, $exception->getCode());
        } catch (Exception) {
            return Result::from(false, 'An error occurred', null, 500);
        }

        return Result::from(true, 'Channel updated successfully', null, 200);
    }
}
