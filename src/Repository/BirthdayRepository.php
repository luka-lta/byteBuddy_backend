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
        private readonly PDO $pdo,
        private readonly GuildRepository $guildRepository,
    ) {
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function getBirthday(string $guildId, string $userId): BirthdayObject
    {
        if (!$this->guildRepository->guildExists($guildId)) {
            throw new ByteBuddyDatabaseException('Guild does not exist', 404);
        }

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

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function getAllBirthdays(string $guildId): array
    {
        $sql = "SELECT user_id, birthday FROM birthday_data WHERE guild_id = :guildId";

        if (!$this->guildRepository->guildExists($guildId)) {
            throw new ByteBuddyDatabaseException('Guild does not exist', 404);
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['guildId' => $guildId]);

            if ($stmt->rowCount() === 0) {
                throw new ByteBuddyDatabaseException('No birthdays found', 404);
            }

            return $stmt->fetchAll();
        } catch (PDOException) {
            throw new ByteBuddyDatabaseException('Failed to fetch birthday data', 500);
        }
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
            $stmt->execute([
                'guildId' => $birthday->getGuildId(),
                'userId' => $birthday->getUserId(),
                'birthday' => $birthday->getBirthday()->format('Y-m-d')
            ]);
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
        if (!$this->guildRepository->guildExists($guildId)) {
            throw new ByteBuddyDatabaseException('Guild does not exist', 404);
        }

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
