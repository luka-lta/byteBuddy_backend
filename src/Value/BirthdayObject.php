<?php
declare(strict_types=1);

namespace ByteBuddyApi\Value;

use DateTime;

final class BirthdayObject
{
    private function __construct(
        private readonly int $guildId,
        private readonly int $userId,
        private readonly DateTime $birthday
    )
    {
    }

    public static function from(int $guildId, int $userId, DateTime $birthday): self
    {
        return new self($guildId, $userId, $birthday);
    }

    public function asArray(): array
    {
        return [
            'guildId' => $this->guildId,
            'userId' => $this->userId,
            'birthday' => $this->birthday->format('Y-m-d')
        ];
    }

    public function getGuildId(): int
    {
        return $this->guildId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getBirthday(): DateTime
    {
        return $this->birthday;
    }
}