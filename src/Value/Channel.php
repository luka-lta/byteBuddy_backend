<?php
declare(strict_types=1);

namespace ByteBuddyApi\Value;

final class Channel
{
    private function __construct(
        private readonly int $channelId,
    ) {}

    public static function from(
        int $channelId
    ): self {
        return new self($channelId);
    }

    public static function fromDatabase(
        array $data
    ): self {
        return new self(
            $data['channel_id'],
        );
    }

    public function asArray(): array
    {
        return [
            'channelId' => $this->channelId
        ];
    }

    public function getChannelId(): int
    {
        return $this->channelId;
    }
}