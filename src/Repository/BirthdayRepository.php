<?php
declare(strict_types=1);

namespace ByteBuddyApi\Repository;

use ByteBuddyApi\Exception\ByteBuddyDatabaseException;
use ByteBuddyApi\Value\BirthdayObject;
use PDO;
use PDOException;

class BirthdayRepository
{
    public function __construct(
        private readonly PDO $pdo
    )
    {
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function getBirthday(string $guildId, string $userId): BirthdayObject
    {
        $sql = "SELECT birthday FROM birthday_data WHERE guild_id = :guildId AND user_id = :userId";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'guildId' => $guildId,
                'userId' => $userId
            ]);
        } catch (PDOException) {
            throw new ByteBuddyDatabaseException('Failed to fetch birthday data', 500);
        }

        return BirthdayObject::from($guildId, $userId, $stmt->fetchColumn());
    }

    public function getAllBirthdays(string $guildId): array|false
    {
        $sql = "SELECT user_id, birthday FROM birthday_data WHERE guild_id = :guildId";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['guildId' => $guildId]);

            if ($stmt->rowCount() > 0)
                return $stmt->fetchAll();
        } catch (PDOException) {
            throw new ByteBuddyDatabaseException('Failed to fetch birthday data', 500);
        }

        return false;
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function setOrUpdateBirthday(BirthdayObject $birthday): bool
    {
        $sql = "INSERT INTO birthday_data (guild_id, user_id, birthday) VALUES (:guildId, :userId, :birthday)";

        if ($this->birthdayExists($birthday->getGuildId(), $birthday->getUserId())) {
            $sql = "UPDATE birthday_data SET birthday = :birthday WHERE guild_id = :guildId AND user_id = :userId";
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute([
                'guildId' => $birthday->getGuildId(),
                'userId' => $birthday->getUserId(),
                'birthday' => $birthday->getBirthday()->format('Y-m-d')
            ])) {
                return true;
            }
        } catch (PDOException) {
            throw new ByteBuddyDatabaseException('Failed to set birthday data', 500);
        }

        return false;
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function birthdayExists(string $guildId, string $userId): bool
    {
        $sql = "SELECT COUNT(*) FROM birthday_data WHERE guild_id = :guildId AND user_id = :userId";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'guildId' => $guildId,
                'userId' => $userId
            ]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException) {
            throw new ByteBuddyDatabaseException('Failed to check if birthday data exists', 500);
        }
    }
}