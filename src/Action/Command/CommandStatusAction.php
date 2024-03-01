<?php
declare(strict_types=1);

namespace ByteBuddyApi\Action\Command;

use ByteBuddyApi\Action\ByteBuddyAction;
use ByteBuddyApi\Service\CommandStatusService;
use ByteBuddyApi\Value\ResultObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CommandStatusAction extends ByteBuddyAction
{
    public function __construct(private readonly CommandStatusService $commandStatusService)
    {
    }

    public function handleGetDisabledCommandsAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $result = $this->commandStatusService->getDisabledCommand();
        return $this->buildResponse($response, $result);
    }

    public function handleEnableCommandAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $name = $request->getQueryParams()['name'] ?? null;

        if ($name === null) {
            $result = ResultObject::from(false, 'Name is required', null, 400);
            return $this->buildResponse($response, $result);
        }

        $result = $this->commandStatusService->enableCommand($name);
        return $this->buildResponse($response, $result);
    }

    public function handleDisableCommandAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $name = $request->getQueryParams()['name'] ?? null;

        if ($name === null) {
            $result = ResultObject::from(false, 'Name ID is required', null, 400);
            return $this->buildResponse($response, $result);
        }

        $result = $this->commandStatusService->disableCommand($name);
        return $this->buildResponse($response, $result);
    }
}