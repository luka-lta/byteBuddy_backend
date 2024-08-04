<?php

declare(strict_types=1);

namespace ByteBuddyApi\V1\Service\Results;

use ByteBuddyApi\V1\Exception\ByteBuddyException;
use ByteBuddyApi\V1\Repository\CommandRepository;
use ByteBuddyApi\V1\Value\Command;
use ByteBuddyApi\V1\Value\Result;
use Monolog\Logger;

class CommandService
{
    public function __construct(private readonly CommandRepository $commandRepository, private readonly Logger $logger,)
    {
    }

    public function registerNewCommands(array $commandsData): Result
    {
        try {
            $commands = [];
            foreach ($commandsData as $commandData) {
                $command = Command::fromArray($commandData);
                $commands[] = $command;
            }

            $this->commandRepository->registerNewCommands($commands);
            return Result::from(true, 'Command registered', null, 201);
        } catch (ByteBuddyException $exception) {
            $this->logger->error('Failed to register new command', ['exception' => $exception->getPrevious()]);
            return Result::from(false, $exception->getMessage(), null, $exception->getCode());
        }
    }

    public function deleteCommand(string $name): Result
    {
        try {
            $this->commandRepository->deleteCommand($name);
            return Result::from(true, 'Command deleted', null, 200);
        } catch (ByteBuddyException $exception) {
            return Result::from(false, $exception->getMessage(), null, $exception->getCode());
        }
    }

    public function getAvailableCommands(int $page, int $limit): Result
    {
        try {
            $result = $this->commandRepository->getAvailableCommands($page, $limit);
            if (!$result) {
                return Result::from(true, 'No available commands', null, 404);
            }

            return Result::from(true, 'Available commands retrieved', $result, 200);
        } catch (ByteBuddyException $exception) {
            return Result::from(false, $exception->getMessage(), null, $exception->getCode());
        }
    }

    public function getAllCommands(int $page, int $limit): Result
    {
        try {
            $result = $this->commandRepository->getAllCommands($page, $limit);
            if (!$result) {
                return Result::from(true, 'No commands found', null, 404);
            }

            return Result::from(true, 'Commands found', $result, 200);
        } catch (ByteBuddyException $exception) {
            return Result::from(false, $exception->getMessage(), null, $exception->getCode());
        }
    }

    public function getDisabledCommand(int $page, int $limit): Result
    {
        try {
            $result = $this->commandRepository->getDisabledCommands($page, $limit);
            if (!$result) {
                return Result::from(true, 'No disabled commands found', null, 404);
            }

            return Result::from(true, 'Disabled commands found', $result, 200);
        } catch (ByteBuddyException $exception) {
            return Result::from(false, $exception->getMessage(), null, $exception->getCode());
        }
    }

    public function toggleCommandById(int $commandId): Result
    {
        try {
            $state = $this->commandRepository->toggleCommandById($commandId);
            $message = $state ? 'Command enabled' : 'Command disabled';
            return Result::from(true, $message, null, 200);
        } catch (ByteBuddyException $exception) {
            return Result::from(false, $exception->getMessage(), null, $exception->getCode());
        }
    }

    public function toggleCommandByName(string $name): Result
    {
        try {
            $state = $this->commandRepository->toggleCommandByName($name);
            $message = $state ? 'Command disabled' : 'Command enabled';
            return Result::from(true, $message, null, 200);
        } catch (ByteBuddyException $exception) {
            return Result::from(false, $exception->getMessage(), null, $exception->getCode());
        }
    }
}
