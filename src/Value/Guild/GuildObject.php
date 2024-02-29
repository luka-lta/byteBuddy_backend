<?php
declare(strict_types=1);

namespace ByteBuddyApi\Value\Guild;

final class GuildObject
{
    private function __construct(
        private readonly string $guildId,
        private readonly string $severName,
        private readonly string $themeColor,
    ) {}

    public static function from(
        string $guildId,
        string $severName,
        string $themeColor,
    ): self {
        return new self(
            $guildId,
            $severName,
            $themeColor,
        );
    }

    public static function fromDatabase(array $data): self
    {
        return new self(
            $data['guild_id'],
            $data['server_name'],
            $data['theme_color'],
        );
    }

    public function asArray(): array
    {
        return [
            'guildId' => $this->guildId,
            'serverName' => $this->severName,
            'themeColor' => '#' . $this->themeColor,
        ];
    }

    public function getGuildId(): string
    {
        return $this->guildId;
    }

    public function getSeverName(): string
    {
        return $this->severName;
    }

    public function getThemeColor(): string
    {
        return $this->themeColor;
    }
}