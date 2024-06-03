<?php

declare(strict_types=1);

namespace ByteBuddyApi\Utils;

use ByteBuddyApi\Exception\ByteBuddyDatabaseException;
use DI\DependencyException;
use JetBrains\PhpStorm\Language;
use PDO;
use PDOException;
use PDOStatement;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class PdoUtil
{
    private ?PDO $pdo = null;

    private const HASH_ALGO = 'fnv164';
    private array $statementCache = [];

    public function __construct(private readonly ContainerInterface $container)
    {
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function exec(string $statement): bool
    {
        $this->checkIfPdoInitialize();
        return $this->pdo->exec($statement);
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function query(#[Language('SQL')] string $statement): bool|PDOStatement
    {
        $this->checkIfPdoInitialize();
        return $this->pdo->query($statement);
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function prepare(#[Language('SQL')] string $query, array $options = []): PDOStatement
    {
        $cacheKey = hash(self::HASH_ALGO, $query);

        if (!isset($this->statementCache[$cacheKey])) {
            $this->checkIfPdoInitialize();
            $this->statementCache[$cacheKey] = $this->pdo->prepare($query, $options);
        }

        return $this->statementCache[$cacheKey];
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function lastInsertId(string $name = null): string|false
    {
        $this->checkIfPdoInitialize();
        return $this->pdo->lastInsertId($name);
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function checkIfPdoInitialize(): void
    {
        if ($this->pdo === null) {
            try {
                $this->pdo = $this->container->get(PDO::class);
            } catch (DependencyException | ContainerExceptionInterface | NotFoundExceptionInterface $e) {
                throw new ByteBuddyDatabaseException(
                    'Failed to initialize PDO',
                    500,
                    previousException: $e
                );
            }
        }
    }
}
