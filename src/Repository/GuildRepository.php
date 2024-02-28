<?php
declare(strict_types=1);

namespace ByteBuddyApi\Repository;

use ByteBuddyApi\Exception\ByteBuddyDatabaseException;
use ByteBuddyApi\Value\Config\GuildObject;
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
    public function registerNewGuild(int $guildId, string $serverName): bool
    {
        $sql = <<<SQL
            INSERT INTO guild_data (guild_id, server_name) VALUES (:guildId, :serverId)
        SQL;

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
    public function getConfigData(int $guildId): GuildObject
    {
        $sql = <<<SQL
            SELECT * FROM guild_data WHERE guild_id = $guildId
        SQL;

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
    public function setConfigKey(int $guildId, string $row, string $value): bool
    {
        $sql = <<<SQL
            UPDATE guild_data
            SET $row = :value WHERE guild_id = :guildId
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);

            if ($stmt->execute([
                'value' => $value,
                'guildId' => $guildId
            ])) {
                return true;
            }

            return false;
        } catch (PDOException) {
            throw new ByteBuddyDatabaseException('Failed to fetch config data', 500);
        }
    }
}