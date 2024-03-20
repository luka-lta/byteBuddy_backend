<?php
declare(strict_types=1);

namespace ByteBuddyApi\Service;

use ByteBuddyApi\Exception\ByteBuddyException;
use ByteBuddyApi\Repository\CommandRepository;
use ByteBuddyApi\Value\Command;
use ByteBuddyApi\Value\ResultObject;
use Monolog\Logger;

class CommandService
{
    public function __construct(
        private readonly CommandRepository $commandRepository,
        private readonly Logger $logger,
    )
    {
    }

    public function registerNewCommands(array $commandsData): ResultObject
    {
        try {
            $commands = [];

            foreach ($commandsData as $commandData) {
                $command = Command::fromArray($commandData);
                $commands[] = $command;
            }

            $this->commandRepository->registerNewCommands($commands);
            return ResultObject::from(true, 'Command registered', null, 201);
        } catch (ByteBuddyException $exception) {
            $this->logger->error('Failed to register new command', ['exception' => $exception->getPrevious()]);
            return ResultObject::from(false, $exception->getMessage(), null, $exception->getCode());
        }
    }

    public function deleteCommand(string $name): ResultObject
    {
        try {
            $this->commandRepository->deleteCommand($name);
            return ResultObject::from(true, 'Command deleted', null, 200);
        } catch (ByteBuddyException $exception) {
            return ResultObject::from(false, $exception->getMessage(), null, $exception->getCode());
        }
    }

    public function getAvailableCommands(): ResultObject
    {
        try {
            $result = $this->commandRepository->getAvailableCommands();
            return ResultObject::from(true, 'Available commands retrieved', $result, 200);
        } catch (ByteBuddyException $exception) {
            return ResultObject::from(false, $exception->getMessage(), null, $exception->getCode());
        }
    }

    public function getAllCommands(): ResultObject
    {
        try {
            $result = $this->commandRepository->getAllCommands();
            return ResultObject::from(true, 'All commands retrieved', $result, 200);
        } catch (ByteBuddyException $exception) {
            return ResultObject::from(false, $exception->getMessage(), null, $exception->getCode());
        }
    }

    public function getDisabledCommand(): ResultObject
    {
        try {
            $result = $this->commandRepository->getDisabledCommands();
            return ResultObject::from(true, 'Disabled commands retrieved', $result, 200);
        } catch (ByteBuddyException $exception) {
            return ResultObject::from(false, $exception->getMessage(), null, $exception->getCode());
        }
    }

    public function enableCommand(string $name): ResultObject
    {
        try {
            $this->commandRepository->enableCommand($name);
            return ResultObject::from(true, 'Command enabled', null, 200);
        } catch (ByteBuddyException $exception) {
            return ResultObject::from(false, $exception->getMessage(), null, $exception->getCode());
        }
    }

    public function disableCommand(string $name): ResultObject
    {
        try {
            $this->commandRepository->disableCommand($name);
            return ResultObject::from(true, 'Command disabled', null, 200);
        } catch (ByteBuddyException $exception) {
            return ResultObject::from(false, $exception->getMessage(), null, $exception->getCode());
        }
    }
}