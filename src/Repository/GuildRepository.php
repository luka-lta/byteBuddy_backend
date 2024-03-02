<?php
declare(strict_types=1);

namespace ByteBuddyApi\Repository;

use ByteBuddyApi\Exception\ByteBuddyDatabaseException;
use ByteBuddyApi\Value\Guild\GuildObject;
use PDO;
use PDOException;

class GuildRepository
{
    public function __construct(
        private readonly PDO $pdo
    )
    {
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function registerNewGuild(string $guildId, string $serverName): bool
    {
        $sql = <<<SQL
            INSERT INTO guild_data (guild_id, server_name) VALUES (:guildId, :serverId)
        SQL;

        if ($this->guildExists($guildId)) {
            throw new ByteBuddyDatabaseException('Guild already exists', 400);
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'guildId' => $guildId,
                'serverId' => $serverName
            ]);
        } catch (PDOException) {
            throw new ByteBuddyDatabaseException('Failed to register new guild', 500);
        }
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function getConfigData(string $guildId): GuildObject
    {
        $sql = <<<SQL
            SELECT * FROM guild_data WHERE guild_id = $guildId
        SQL;

        if (!$this->guildExists($guildId)) {
            throw new ByteBuddyDatabaseException('Guild does not exist', 404);
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetch();
        } catch (PDOException) {
            throw new ByteBuddyDatabaseException('Failed to fetch config data', 500);
        }

        return GuildObject::fromDatabase($result);
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function setConfigKey(string $guildId, string $row, string $value): void
    {
        $sql = <<<SQL
            UPDATE guild_data
            SET $row = :value WHERE guild_id = :guildId
        SQL;

        if (!$this->guildExists($guildId)) {
            throw new ByteBuddyDatabaseException('Guild does not exist', 404);
        }

        try {
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                'value' => $value,
                'guildId' => $guildId
            ]);
        } catch (PDOException) {
            throw new ByteBuddyDatabaseException('Failed to fetch config data', 500);
        }
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function guildExists(string $guildId): bool
    {
        $sql = <<<SQL
            SELECT guild_id FROM guild_data WHERE guild_id = :guildId
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['guildId' => $guildId]);

            return $stmt->fetch() !== false;
        } catch (PDOException) {
            throw new ByteBuddyDatabaseException('Failed to check if guild exists', 500);
        }
    }
}