<?php

declare(strict_types=1);

namespace ByteBuddyApi\V1\Repository;

use ByteBuddyApi\V1\Exception\ByteBuddyCommandNotFoundException;
use ByteBuddyApi\V1\Exception\ByteBuddyDatabaseException;
use ByteBuddyApi\V1\Service\PaginationService;
use ByteBuddyApi\V1\Utils\PdoUtil;
use ByteBuddyApi\V1\Value\Command;
use PDOException;

class CommandRepository
{
    public function __construct(
        private readonly PdoUtil           $pdo,
        private readonly PaginationService $paginationService
    ) {
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function registerNewCommands(array $commands): void
    {
        $insertSql = <<<SQL
        INSERT INTO command_data (name, description, disabled) VALUES (:name, :description, :disabled)
        SQL;
        $updateSql = <<<SQL
        UPDATE command_data SET description = :description, disabled = :disabled WHERE name = :name
        SQL;
        $existingCommands = [];

        /** @var Command $command */
        foreach ($commands as $command) {
            $existingCommands[] = $command->getName();
            if ($this->commandExists($command->getName())) {
                $stmt = $this->pdo->query($updateSql);
                $stmt->execute([
                    'name' => $command->getName(),
                    'description' => $command->getDescription(),
                    'disabled' => $command->isDisabled() ? 1 : 0
                ]);
                continue;
            }

            try {
                $stmt = $this->pdo->query($insertSql);
                $stmt->execute([
                    'name' => $command->getName(),
                    'description' => $command->getDescription(),
                    'disabled' => $command->isDisabled() ? 1 : 0
                ]);
            } catch (PDOException $exception) {
                throw new ByteBuddyDatabaseException('Failed to register new command', 500, $exception);
            }
        }

        $this->deleteObsoleteCommands($existingCommands);
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
    public function getAvailableCommands(int $page = 1, int $itemsPerPage = 10): array|null
    {
        $sql = <<<SQL
             SELECT * FROM command_data WHERE disabled = 0 LIMIT :limit OFFSET :offset
        SQL;
        $countSql = "SELECT COUNT(*) as count FROM command_data WHERE disabled = 0";
        return $this->fetchCommands($sql, $countSql, $page, $itemsPerPage);
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function getAllCommands(int $page = 1, int $itemsPerPage = 10): array|null
    {
        $sql = "SELECT * FROM command_data LIMIT :limit OFFSET :offset";
        $countSql = "SELECT COUNT(*) as count FROM command_data";
        return $this->fetchCommands($sql, $countSql, $page, $itemsPerPage);
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function getDisabledCommands(int $page = 1, int $itemsPerPage = 10): array|null
    {
        $sql = "SELECT * FROM command_data WHERE disabled = 1 LIMIT :limit OFFSET :offset";
        $countSql = "SELECT COUNT(*) as count FROM command_data WHERE disabled = 1";
        return $this->fetchCommands($sql, $countSql, $page, $itemsPerPage);
    }

    /**
     * @throws ByteBuddyDatabaseException
     * @throws ByteBuddyCommandNotFoundException
     */
    public function toggleCommandById(int $commandId): bool
    {
        if (!$this->commandExistsById($commandId)) {
            throw new ByteBuddyCommandNotFoundException('Command not found', 404);
        }

        $sql = <<<SQL
            UPDATE command_data SET disabled = NOT disabled WHERE id = :id
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $commandId]);

            $statusSql = <<<SQL
                SELECT disabled FROM command_data WHERE id = :id
            SQL;

            $stmt = $this->pdo->prepare($statusSql);
            $stmt->execute(['id' => $commandId]);
            return (bool)$stmt->fetchColumn();
        } catch (PDOException) {
            throw new ByteBuddyDatabaseException('Failed to toggle command', 500);
        }
    }

    /**
     * @throws ByteBuddyDatabaseException|ByteBuddyCommandNotFoundException
     */
    public function toggleCommandByName(string $name): bool
    {
        if (!$this->commandExists($name)) {
            throw new ByteBuddyCommandNotFoundException('Command not found', 404);
        }

        $sql = <<<SQL
            UPDATE command_data SET disabled = NOT disabled WHERE name = :name
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['name' => $name]);

            $statusSql = <<<SQL
                SELECT disabled FROM command_data WHERE name = :name
            SQL;

            $stmt = $this->pdo->prepare($statusSql);
            $stmt->execute(['name' => $name]);
            return (bool)$stmt->fetchColumn();
        } catch (PDOException) {
            throw new ByteBuddyDatabaseException('Failed to toggle command', 500);
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

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function commandExistsById(int $commmandId): bool
    {
        $sql = <<<SQL
            SELECT * FROM command_data WHERE id = :id
        SQL;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $commmandId]);

            return $stmt->rowCount() > 0;
        } catch (PDOException) {
            throw new ByteBuddyDatabaseException('Failed to check if command exists', 500);
        }
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    private function fetchCommands(string $sql, string $countSql, int $page, int $itemsPerPage): array|null
    {
        $offset = ($page - 1) * $itemsPerPage;

        try {
            $rowStmt = $this->pdo->prepare($sql);
            $rowStmt->execute();
            $totalItems = $rowStmt->fetch()['count'];

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'limit' => $itemsPerPage,
                'offset' => $offset
            ]);

            if ($stmt->rowCount() === 0) {
                return null;
            }

            $commandsData = $stmt->fetchAll();
        } catch (PDOException $exception) {
            throw new ByteBuddyDatabaseException('Failed to get commands', 500, previousException: $exception);
        }

        $commands = [];
        foreach ($commandsData as $command) {
            $userObject = Command::fromDatabase($command);
            $commands[] = $userObject->toArray();
        }

        $pagination = $this->paginationService->paginate($totalItems, $page, $itemsPerPage);

        return [
            'pagination' => $pagination,
            'commands' => $commands,
        ];
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    private function deleteObsoleteCommands(array $existingCommands): void
    {
        $placeholders = implode(', ', array_fill(0, count($existingCommands), '?'));
        $sql = "DELETE FROM command_data WHERE name NOT IN ($placeholders)";
        $this->pdo->prepare($sql)->execute($existingCommands);
    }
}
