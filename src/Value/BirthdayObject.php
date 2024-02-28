<?php
declare(strict_types=1);

namespace ByteBuddyApi\Value;

use DateTime;

final class BirthdayObject
{
    private function __construct(
        private readonly string $guildId,
        private readonly string $userId,
        private readonly DateTime $birthday
    )
    {
    }

    public static function from(string $guildId, string $userId, DateTime $birthday): self
    {
        return new self($guildId, $userId, $birthday);
    }

    public function asArray(): array
    {
        return [
            'guildId' => $this->guildId,
            'userId' => $this->userId,
            'birthday' => $this->birthday->format('d.m.Y')
        ];
    }

    public function getGuildId(): string
    {
        return $this->guildId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getBirthday(): DateTime
    {
        return $this->birthday;
    }
}