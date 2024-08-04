<?php

declare(strict_types=1);

namespace ByteBuddyApi\V1\Repository;

use ByteBuddyApi\V1\Exception\ByteBuddyDatabaseException;
use ByteBuddyApi\V1\Utils\PdoUtil;
use DateTime;

class CommandHistoryRepository
{
    public function __construct(
        private readonly PdoUtil $pdo,
    ) {
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function createHistory(int $userId, int $guildId, string $command): void
    {
        $sql = <<<SQL
            INSERT INTO command_history (user_id, guild_id, command) VALUES (:userId, :guildId, :command)
        SQL;

        try {
            $this->pdo->execute($sql, [
                'userId' => $userId,
                'guildId' => $guildId,
                'command' => $command,
            ]);
        } catch (ByteBuddyDatabaseException $e) {
            throw new ByteBuddyDatabaseException(
                'Failed to create command history',
                $e->getCode(),
                previousException: $e
            );
        }
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function getHistoryByDateRange(DateTime $startDate, DateTime $endDate): array
    {
        $sql = <<<SQL
            SELECT * FROM command_history WHERE executed_at BETWEEN :startDate AND :endDate
        SQL;

        try {
            $data = $this->pdo->fetchAllQuery($sql, [
                'startDate' => $startDate->format('Y-m-d H:i:s'),
                'endDate' => $endDate->format('Y-m-d H:i:s'),
            ]);
        } catch (ByteBuddyDatabaseException $e) {
            throw new ByteBuddyDatabaseException(
                'Failed to get command history',
                $e->getCode(),
                previousException: $e
            );
        }

        return $data;
    }
}
