<?php
declare(strict_types=1);

namespace ByteBuddyApi\Service;

use ByteBuddyApi\Exception\ByteBuddyException;
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

    public function getAllChannels(int|null $guildId): ResultObject
    {
        if ($guildId == null) {
            return ResultObject::from(false, 'GuildId must be set', null, 400);
        }

        try {
            $welcomeChannel = $this->channelConfigRepository->getChannel($guildId, 'welcome');
            $leaveChannel = $this->channelConfigRepository->getChannel($guildId, 'leave');
            $birthdayChannel = $this->channelConfigRepository->getChannel($guildId, 'birthday');

            return ResultObject::from(
                true,
                'Channels fetched successfully',
                [
                    'welcomeChannel' => $welcomeChannel->getChannelId(),
                    'leaveChannel' => $leaveChannel->getChannelId(),
                    'birthdayChannel' => $birthdayChannel->getChannelId(),
                ],
                200
            );

        } catch (ByteBuddyException $exception) {
            return ResultObject::from(false, $exception->getMessage(), null, $exception->getCode());
        } catch (Exception) {
            return ResultObject::from(false, 'An error occurred', null, 500);
        }
    }

    public function setChannel(int|null $guildId, string|null $channelType, int $channelId): ResultObject
    {
        if ($guildId == null) {
            return ResultObject::from(false, 'GuildId must be set', null, 400);
        }

        if ($channelType == null) {
            return ResultObject::from(false, 'Channel type must be set', null, 400);
        }

        $channel = Channel::from($channelId);
        try {
            $this->channelConfigRepository->setChannel($guildId, $channel, $channelType);
        } catch (ByteBuddyException $exception) {
            return ResultObject::from(false, $exception->getMessage(), null, $exception->getCode());
        } catch (Exception) {
            return ResultObject::from(false, 'An error occurred', null, 500);
        }

        return ResultObject::from(true, 'Channel updated successfully', null, 200);
    }
}