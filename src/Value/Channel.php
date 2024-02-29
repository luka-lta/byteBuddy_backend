<?php
declare(strict_types=1);

namespace ByteBuddyApi\Value;

use ByteBuddyApi\Exception\ByteBuddyInvalidChannelException;
use ByteBuddyApi\Type\ChannelTypes;

final class Channel
{
    /**
     * @throws ByteBuddyInvalidChannelException
     */
    private function __construct(
        private readonly string $channelId,
        private readonly string $channelType,
    )
    {
        if (!in_array($channelType, ChannelTypes::getAllChannelTypes())) {
            throw new ByteBuddyInvalidChannelException('Invalid channel type', 400);
        }
    }

    /**
     * @throws ByteBuddyInvalidChannelException
     */
    public static function from(
        string $channelId,
        string $channelType,
    ): self
    {
        return new self($channelId, $channelType);
    }

    /**
     * @throws ByteBuddyInvalidChannelException
     */
    public static function fromDatabase(
        array  $data,
        string $channelType,
    ): self
    {
        return new self(
            $data['channel_id'],
            $channelType,
        );
    }

    public function asArray(): array
    {
        return [
            'channelId' => $this->channelId,
            'channelType' => $this->channelType,
        ];
    }

    public function getChannelId(): string
    {
        return $this->channelId;
    }

    /**
     * @return string
     */
    public function getChannelType(): string
    {
        return $this->channelType;
    }
}