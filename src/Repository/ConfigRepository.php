<?php
declare(strict_types=1);

namespace ByteBuddyApi\Repository;

use ByteBuddyApi\Exception\ByteBuddyDatabaseException;
use ByteBuddyApi\Value\Config\ConfigObject;
use PDO;
use PDOException;

class ConfigRepository
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
            INSERT INTO config_data (guild_id, server_name) VALUES (:guildId, :serverId)
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
    public function getConfigData(int $guildId): ConfigObject
    {
        $sql = <<<SQL
            SELECT * FROM config_data WHERE guild_id = $guildId
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetch();
        } catch (PDOException) {
            throw new ByteBuddyDatabaseException('Failed to fetch config data', 500);
        }

        return ConfigObject::fromDatabase($result);
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function setConfigKey(int $guildId, string $row, string $value): bool
    {
        $sql = <<<SQL
            UPDATE config_data
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
        } catch (PDOException $exception) {
            var_dump($exception->getMessage());
            throw new ByteBuddyDatabaseException('Failed to fetch config data', 500);
        }
    }
}