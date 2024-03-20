<?php
declare(strict_types=1);

namespace ByteBuddyApi\Repository;

use ByteBuddyApi\Exception\ByteBuddyDatabaseException;
use ByteBuddyApi\Exception\ByteBuddyInvalidChannelException;
use ByteBuddyApi\Type\ChannelTypes;
use ByteBuddyApi\Value\Channel;
use PDO;
use PDOException;

class ChannelConfigRepository
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly GuildRepository $guildRepository,
    ) {
    }

    /**
     * @throws ByteBuddyInvalidChannelException
     * @throws ByteBuddyDatabaseException
     */
    public function getAllChannels(string $guildId): array
    {
        $channelTypes = ChannelTypes::getAllChannelTypes();

        $channels = [];

        foreach ($channelTypes as $channelType) {
            $channels[] = $this->getChannel($guildId, $channelType);
        }

        return $channels;
    }

    /**
     * @throws ByteBuddyDatabaseException
     * @throws ByteBuddyInvalidChannelException
     */
    public function getChannel(string $guildId, string $channelType): Channel
    {
        if (!$this->guildRepository->guildExists($guildId)) {
            throw new ByteBuddyDatabaseException('Guild does not exist', 404);
        }

        $this->validateChannelType($channelType);

        $columnName = "{$channelType}_channel_id";

        $sql = "SELECT $columnName FROM channel_data WHERE guild_id = :guildId";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'guildId' => $guildId
            ]);

            if ($stmt->rowCount() === 0) {
                throw new ByteBuddyDatabaseException('No channels found', 404);
            }

            $result = $stmt->fetch()[$columnName];
            return Channel::from($result, $channelType);
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

        if (!$this->guildRepository->guildExists($guildId)) {
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
        if (!in_array($channelType, ChannelTypes::getAllChannelTypes())) {
            throw new ByteBuddyInvalidChannelException('Invalid channel type', 400);
        }
    }
}
