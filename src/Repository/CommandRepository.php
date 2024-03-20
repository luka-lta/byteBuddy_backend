<?php
declare(strict_types=1);

namespace ByteBuddyApi\Repository;

use ByteBuddyApi\Exception\ByteBuddyCommandAlreadyDisabledException;
use ByteBuddyApi\Exception\ByteBuddyCommandNotFoundException;
use ByteBuddyApi\Exception\ByteBuddyDatabaseException;
use ByteBuddyApi\Value\Command;
use PDO;
use PDOException;

class CommandRepository
{
    public function __construct(
        private readonly PDO $pdo
    )
    {
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function registerNewCommands(array $commands): void
    {
        // TODO: Add a check if command old and can be deleted
        $insertSql = <<<SQL
        INSERT INTO command_data (name, description, disabled) VALUES (:name, :description, :disabled)
        SQL;

        $updateSql = <<<SQL
        UPDATE command_data SET description = :description, disabled = :disabled WHERE name = :name
        SQL;

        $insertStmt = $this->pdo->prepare($insertSql);
        $updateStmt = $this->pdo->prepare($updateSql);

        /** @var Command $command */
        foreach ($commands as $command) {

            if ($this->commandExists($command->getName())) {
                $updateStmt->execute([
                    'name' => $command->getName(),
                    'description' => $command->getDescription(),
                    'disabled' => $command->isDisabled() ? 1 : 0
                ]);
                continue;
            }

            try {
                $insertStmt->execute([
                    'name' => $command->getName(),
                    'description' => $command->getDescription(),
                    'disabled' => $command->isDisabled() ? 1 : 0
                ]);
            } catch (PDOException $exception) {
                throw new ByteBuddyDatabaseException('Failed to register new command', 500, $exception);
            }
        }
    }


    /**
     * @throws ByteBuddyCommandNotFoundException
     * @throws ByteBuddyDatabaseException
     */
    public function deleteCommand(string $name): void
    {
        $sql = <<<SQL
            DELETE FROM command_data WHERE name = :name
        SQL;

        if (!$this->commandExists($name)) {
            throw new ByteBuddyCommandNotFoundException('Command not found', 404);
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['name' => $name]);
        } catch (PDOException) {
            throw new ByteBuddyDatabaseException('Failed to delete command', 500);
        }
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function getAvailableCommands(): array
    {
        $sql = <<<SQL
            SELECT * FROM command_data WHERE disabled = 0
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException) {
            throw new ByteBuddyDatabaseException('Failed to get available commands', 500);
        }
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function getAllCommands(): array
    {
        $sql = <<<SQL
            SELECT * FROM command_data
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException) {
            throw new ByteBuddyDatabaseException('Failed to get all commands', 500);
        }
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function getDisabledCommands(): array
    {
        $sql = <<<SQL
            SELECT * FROM command_data WHERE disabled = 1
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
     * @throws ByteBuddyCommandNotFoundException
     */
    public function enableCommand(string $name): void
    {
        $sql = <<<SQL
            UPDATE command_data SET disabled = 0 WHERE name = :name
        SQL;

        try {
            if (!$this->commandExists($name)) {
                throw new ByteBuddyCommandNotFoundException('Command not found', 404);
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['name' => $name]);
        } catch (PDOException) {
            throw new ByteBuddyDatabaseException('Failed to enable command', 500);
        }
    }

    /**
     * @throws ByteBuddyCommandNotFoundException
     * @throws ByteBuddyDatabaseException
     */
    public function disableCommand(string $name): void
    {
        $sql = <<<SQL
            UPDATE command_data SET disabled = 1 WHERE name = :name
        SQL;

        if (!$this->commandExists($name)) {
            throw new ByteBuddyCommandNotFoundException('Command not found', 404);
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
            SELECT * FROM command_data WHERE name = :name
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