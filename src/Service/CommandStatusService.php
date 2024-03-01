<?php
declare(strict_types=1);

namespace ByteBuddyApi\Service;

use ByteBuddyApi\Exception\ByteBuddyException;
use ByteBuddyApi\Repository\CommandStatusRepository;
use ByteBuddyApi\Value\ResultObject;

class CommandStatusService
{
    public function __construct(
        private readonly CommandStatusRepository $commandStatusRepository,
    )
    {
    }

    public function getDisabledCommand(): ResultObject
    {
        try {
            $result = $this->commandStatusRepository->getDisabledCommands();
            return ResultObject::from(true, 'Disabled commands retrieved', $result, 200);
        } catch (ByteBuddyException $exception) {
            return ResultObject::from(false, $exception->getMessage(), null, $exception->getCode());
        }
    }

    public function enableCommand(string $name): ResultObject
    {
        try {
            $this->commandStatusRepository->enableCommand($name);
            return ResultObject::from(true, 'Command enabled', null, 200);
        } catch (ByteBuddyException $exception) {
            return ResultObject::from(false, $exception->getMessage(), null, $exception->getCode());
        }
    }

    public function disableCommand(string $name): ResultObject
    {
        try {
            $this->commandStatusRepository->disableCommand($name);
            return ResultObject::from(true, 'Command disabled', null, 200);
        } catch (ByteBuddyException $exception) {
            return ResultObject::from(false, $exception->getMessage(), null, $exception->getCode());
        }
    }
}