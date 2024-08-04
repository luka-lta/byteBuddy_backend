<?php

declare(strict_types=1);

namespace ByteBuddyApi\V1\Service\Results;

use ByteBuddyApi\V1\Exception\ByteBuddyException;
use ByteBuddyApi\V1\Repository\CommandHistoryRepository;
use ByteBuddyApi\V1\Value\Result;
use Exception;

class CommandHistoryActionService
{
    public function __construct(
        private readonly CommandHistoryRepository $commandHistoryActionRepository
    ) {
    }

    public function createHistory(int $userId, int $guildId, string $command): Result
    {
        try {
            $this->commandHistoryActionRepository->createHistory($userId, $guildId, $command);
        } catch (ByteBuddyException $exception) {
            return Result::from(false, $exception->getMessage(), null, $exception->getCode());
        } catch (Exception) {
            return Result::from(false, 'An error occurred', null, 500);
        }

        return Result::from(true, 'Command history created successfully', null, 200);
    }
}
