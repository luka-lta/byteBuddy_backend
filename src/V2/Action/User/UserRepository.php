<?php

declare(strict_types=1);

namespace ByteBuddyApi\V2\Action\User;

use ByteBuddyApi\V1\Exception\ByteBuddyDatabaseException;
use ByteBuddyApi\V1\Utils\PdoUtil;
use ByteBuddyApi\V2\Value\User\User;
use PDOException;

class UserRepository
{
    public function __construct(
        private readonly PdoUtil $pdo
    ) {
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function create(User $user): void
    {
        $sql = <<<SQL
            INSERT INTO 
                users (username, email, hashed_password)
            VALUES 
                (:username, :email, :hashedPassword)
            SQL;

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute([
                'username' => $user->getUsername()->getValue(),
                'email' => $user->getEmail()->getValue(),
                'hashed_password' => $user->getPassword()->getValue(),
            ]);
        } catch (PDOException $exception) {
            throw new ByteBuddyDatabaseException(
                $exception->getMessage(),
                $exception->getCode(),
                previousException: $exception
            );
        }
    }
}
