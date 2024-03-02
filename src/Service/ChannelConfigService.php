<?php
declare(strict_types=1);

namespace ByteBuddyApi\Service;

use ByteBuddyApi\Exception\ByteBuddyException;
use ByteBuddyApi\Exception\ByteBuddyInvalidChannelException;
use ByteBuddyApi\Repository\ChannelConfigRepository;
use ByteBuddyApi\Value\Channel;
use ByteBuddyApi\Value\ResultObject;
use Exception;

class ChannelConfigService
{
    public function __construct(
        private readonly ChannelConfigRepository $channelConfigRepository
    )
    {
    }

    public function getAllChannelsOrSpecific(string $guildId, string|null $channelType): ResultObject
    {
        try {
            if ($channelType) {
                $channel = $this->channelConfigRepository->getChannel($guildId, $channelType);

                return ResultObject::from(
                    true,
                    'Channel fetched successfully',
                    $channel->asArray(),
                    200
                );
            }

            $channels = $this->channelConfigRepository->getAllChannels($guildId);

            $channelArray = [];
            /** @var Channel $channel */
            foreach ($channels as $channel) {
                $channelArray[] = $channel->asArray();
            }

            return ResultObject::from(
                true,
                'Channels fetched successfully',
                $channelArray,
                200
            );

        } catch (ByteBuddyException $exception) {
            return ResultObject::from(false, $exception->getMessage(), null, $exception->getCode());
        } catch (Exception) {
            return ResultObject::from(false, 'An error occurred', null, 500);
        }
    }

    public function setChannel(string $guildId, string $channelType, string $channelId): ResultObject
    {
        if ($guildId == null) {
            return ResultObject::from(false, 'GuildId must be set', null, 400);
        }

        if ($channelType == null) {
            return ResultObject::from(false, 'Channel type must be set', null, 400);
        }

        try {
            $channel = Channel::from($channelId, $channelType);
            $this->channelConfigRepository->setChannel($guildId, $channel, $channelType);
        } catch (ByteBuddyException $exception) {
            return ResultObject::from(false, $exception->getMessage(), null, $exception->getCode());
        } catch (Exception) {
            return ResultObject::from(false, 'An error occurred', null, 500);
        }

        return ResultObject::from(true, 'Channel updated successfully', null, 200);
    }
}