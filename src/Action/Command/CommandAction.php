<?php
declare(strict_types=1);

namespace ByteBuddyApi\Action\Command;

use ByteBuddyApi\Action\ByteBuddyAction;
use ByteBuddyApi\Service\CommandService;
use ByteBuddyApi\Value\ResultObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CommandAction extends ByteBuddyAction
{
    public function __construct(private readonly CommandService $commandStatusService)
    {
    }

    public function handleRegisterCommandAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $commandData = $request->getParsedBody();

        if ($commandData === null) {
            $result = ResultObject::from(false, 'Command data is required', null, 400);
            return $this->buildResponse($response, $result);
        }

        $result = $this->commandStatusService->registerNewCommand($commandData);
        return $this->buildResponse($response, $result);
    }

    public function handleGetCommandsAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (isset($request->getQueryParams()['status'])) {
            switch ($request->getQueryParams()['status']) {
                case 'enabled':
                    $result = $this->commandStatusService->getAvailableCommands();
                    return $this->buildResponse($response, $result);
                case 'disabled':
                    $result = $this->commandStatusService->getDisabledCommand();
                    return $this->buildResponse($response, $result);
                default:
                    $result = ResultObject::from(false, 'Invalid status', null, 400);
                    return $this->buildResponse($response, $result);
            }
        }

        $result = $this->commandStatusService->getAllCommands();
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