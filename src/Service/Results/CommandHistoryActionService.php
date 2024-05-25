<?php

declare(strict_types=1);

namespace ByteBuddyApi\Service\Results;

use ByteBuddyApi\Exception\ByteBuddyException;
use ByteBuddyApi\Repository\CommandHistoryRepository;
use ByteBuddyApi\Value\Result;
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
