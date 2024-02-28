<?php
declare(strict_types=1);

namespace ByteBuddyApi\Repository;

use ByteBuddyApi\Exception\ByteBuddyDatabaseException;
use ByteBuddyApi\Exception\ByteBuddyInvalidChannelException;
use ByteBuddyApi\Value\Channel;
use PDO;
use PDOException;

class ChannelConfigRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @throws ByteBuddyDatabaseException
     * @throws ByteBuddyInvalidChannelException
     */
    public function getChannel(string $guildId, string $channelType): Channel
    {
        $this->validateChannelType($channelType);

        $columnName = "{$channelType}_channel_id";

        $sql = "SELECT $columnName FROM channel_data WHERE guild_id = :guildId";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['guildId' => $guildId]);

            $result = $stmt->fetch()[$columnName];

            return Channel::from($result);
        } catch (PDOException) {
            throw new ByteBuddyDatabaseException('Failed to fetch channel data', 500);
        }
    }

    /**
     * @throws ByteBuddyDatabaseException
     * @throws ByteBuddyInvalidChannelException
     */
    public function setChannel(string $guildId, Channel $channel, string $channelType): void
    {
        $this->validateChannelType($channelType);

        if (!$this->guildExists($guildId)) {
            $this->createGuild($guildId);
        }

        $columnName = "{$channelType}_channel_id";

        $sql = "UPDATE channel_data SET $columnName = :channelId WHERE guild_id = :guildId";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'channelId' => $channel->getChannelId(),
                'guildId' => $guildId
            ]);
        } catch (PDOException) {
            throw new ByteBuddyDatabaseException('Failed to update channel data', 500);
        }
    }

    private function guildExists(string $guildId): bool
    {
        $sql = "SELECT guild_id FROM channel_data WHERE guild_id = :guildId";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['guildId' => $guildId]);

        return $stmt->fetch() !== false;
    }

    public function createGuild(string $guildId): void
    {
        $sql = "INSERT INTO channel_data (guild_id) VALUES (:guildId)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['guildId' => $guildId]);
    }

    /**
     * @throws ByteBuddyInvalidChannelException
     */
    private function validateChannelType(string $channelType): void
    {
        if (!in_array($channelType, ['welcome', 'leave', 'birthday'])) {
            throw new ByteBuddyInvalidChannelException('Invalid channel type');
        }
    }
}
