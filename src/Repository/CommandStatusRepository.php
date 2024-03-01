<?php
declare(strict_types=1);

namespace ByteBuddyApi\Repository;

use ByteBuddyApi\Exception\ByteBuddyCommandAlreadyDisabledException;
use ByteBuddyApi\Exception\ByteBuddyCommandIsNotDisabledException;
use ByteBuddyApi\Exception\ByteBuddyDatabaseException;
use PDO;
use PDOException;

class CommandStatusRepository
{
    public function __construct(
        private readonly PDO $pdo
    )
    {
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function getDisabledCommands(): array
    {
        $sql = <<<SQL
            SELECT * FROM disabled_commands
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException) {
            throw new ByteBuddyDatabaseException('Failed to get disabled commands', 500);
        }
    }

    /**
     * @throws ByteBuddyDatabaseException
     * @throws ByteBuddyCommandIsNotDisabledException
     */
    public function enableCommand(string $name): void
    {
        $sql = <<<SQL
            DELETE FROM disabled_commands WHERE name = :name
        SQL;

        try {
            if (!$this->commandExists($name)) {
                throw new ByteBuddyCommandIsNotDisabledException('Command is not disabled', 400);
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['name' => $name]);
        } catch (PDOException) {
            throw new ByteBuddyDatabaseException('Failed to enable command', 500);
        }
    }

    /**
     * @throws ByteBuddyCommandAlreadyDisabledException
     * @throws ByteBuddyDatabaseException
     */
    public function disableCommand(string $name): void
    {
        $sql = <<<SQL
            INSERT INTO disabled_commands (name) VALUES (:name)
        SQL;

        if ($this->commandExists($name)) {
            throw new ByteBuddyCommandAlreadyDisabledException('Command is already disabled', 400);
        }

        try {

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['name' => $name]);
        } catch (PDOException) {
            throw new ByteBuddyDatabaseException('Failed to disable command', 500);
        }
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function commandExists(string $name): bool
    {
        $sql = <<<SQL
            SELECT * FROM disabled_commands WHERE name = :name
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['name' => $name]);
            return $stmt->rowCount() > 0;
        } catch (PDOException) {
            throw new ByteBuddyDatabaseException('Failed to check if command exists', 500);
        }
    }
}