<?php

declare(strict_types=1);

namespace ByteBuddyApi\V1\Type;

class ChannelTypes
{
    public const WELCOME_CHANNEL = 'welcome';
    public const LEAVE_CHANNEL = 'leave';
    public const BIRTHDAY_CHANNEL = 'birthday';
    public static function getAllChannelTypes(): array
    {
        return [
            self::WELCOME_CHANNEL,
            self::LEAVE_CHANNEL,
            self::BIRTHDAY_CHANNEL,
        ];
    }
}
