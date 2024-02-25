<?php
declare(strict_types=1);

namespace ByteBuddyApi\Value\Config;

final class ConfigObject
{
    private function __construct(
        private readonly int $guildId,
        private readonly string $severName,
        private readonly string $themeColor,
    ) {}

    public static function from(
        int $guildId,
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
            'themeColor' => $this->themeColor,
        ];
    }

    public function getGuildId(): int
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