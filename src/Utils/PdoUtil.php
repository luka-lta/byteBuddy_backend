<?php

declare(strict_types=1);

namespace ByteBuddyApi\Utils;

use ByteBuddyApi\Exception\ByteBuddyDatabaseException;
use PDO;
use PDOException;

class PdoUtil
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function execute(string $sql, array $params = []): void
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
        } catch (PDOException $e) {
            var_dump($e->getMessage());
            throw new ByteBuddyDatabaseException(
                'Failed to execute query',
                500,
                $params,
                $e
            );
        }
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function executeUpdate(string $sql, array $params = []): int
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new ByteBuddyDatabaseException(
                'Failed to execute update query',
                500,
                $params,
                $e
            );
        }
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function fetchQuery(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new ByteBuddyDatabaseException(
                'Failed to fetch query',
                500,
                $params,
                $e
            );
        }
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function fetchAllQuery(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new ByteBuddyDatabaseException(
                'Failed to fetch all query',
                500,
                $params,
                $e
            );
        }
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function fetchColumn(string $sql, array $params = []): int
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new ByteBuddyDatabaseException(
                'Failed to fetch column query',
                500,
                $params,
                $e
            );
        }
    }

    public function getLastInsertedId(): int
    {
        return (int)$this->pdo->lastInsertId();
    }
}
