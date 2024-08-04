<?php

declare(strict_types=1);

namespace ByteBuddyApi\V1\Service;

use ByteBuddyApi\V1\Exception\ByteBuddyDatabaseException;
use Exception;
use Monolog\Logger;

class ExceptionService
{
    public function __construct(
        private readonly Logger $logger,
    ) {
    }

    public function handleUserException(Exception $exception): void
    {
        if ($exception instanceof ByteBuddyDatabaseException) {
            $this->logger->error($exception->getMessage(), [
                'code' => $exception->getCode(),
                'params' => $exception->getAdditionalData(),
                'exception' => $exception->getPrevious(),
            ]);
        }
    }
}
