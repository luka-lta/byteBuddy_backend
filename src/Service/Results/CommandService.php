<?php

declare(strict_types=1);

namespace ByteBuddyApi\Service\Results;

use ByteBuddyApi\Exception\ByteBuddyException;
use ByteBuddyApi\Repository\CommandRepository;
use ByteBuddyApi\Value\Command;
use ByteBuddyApi\Value\Result;
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

    public function getAvailableCommands(): Result
    {
        try {
            $result = $this->commandRepository->getAvailableCommands();
            if (!$result) {
                return Result::from(true, 'No available commands', null, 404);
            }

            return Result::from(true, 'Available commands retrieved', $result, 200);
        } catch (ByteBuddyException $exception) {
            return Result::from(false, $exception->getMessage(), null, $exception->getCode());
        }
    }

    public function getAllCommands(): Result
    {
        try {
            $result = $this->commandRepository->getAllCommands();
            if (!$result) {
                return Result::from(true, 'No commands found', null, 404);
            }

            return Result::from(true, 'Commands found', $result, 200);
        } catch (ByteBuddyException $exception) {
            return Result::from(false, $exception->getMessage(), null, $exception->getCode());
        }
    }

    public function getDisabledCommand(): Result
    {
        try {
            $result = $this->commandRepository->getDisabledCommands();
            if (!$result) {
                return Result::from(true, 'No disabled commands found', null, 404);
            }

            return Result::from(true, 'Disabled commands found', $result, 200);
        } catch (ByteBuddyException $exception) {
            return Result::from(false, $exception->getMessage(), null, $exception->getCode());
        }
    }

    public function toggleCommandById(int $id): Result
    {
        try {
            $state = $this->commandRepository->toggleCommandById($id);
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
            $message = $state ? 'Command enabled' : 'Command disabled';
            return Result::from(true, $message, null, 200);
        } catch (ByteBuddyException $exception) {
            return Result::from(false, $exception->getMessage(), null, $exception->getCode());
        }
    }
}
