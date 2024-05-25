<?php

declare(strict_types=1);

namespace ByteBuddyApi\Repository;

use ByteBuddyApi\Exception\ByteBuddyDatabaseException;
use ByteBuddyApi\Utils\PdoUtil;

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
}
