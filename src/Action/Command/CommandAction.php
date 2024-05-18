<?php
declare(strict_types=1);

namespace ByteBuddyApi\Action\Command;

use ByteBuddyApi\Action\ByteBuddyAction;
use ByteBuddyApi\Service\Results\CommandService;
use ByteBuddyApi\Value\Result;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CommandAction extends ByteBuddyAction
{
    public function __construct(private readonly CommandService $commandStatusService)
    {
    }

    public function handleRegisterCommandAction(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $commandData = $request->getParsedBody();

        if ($commandData === null) {
            $result = Result::from(false, 'Command data is required', null, 400);
            return $this->buildResponse($response, $result);
        }

        $result = $this->commandStatusService->registerNewCommands($commandData);
        return $this->buildResponse($response, $result);
    }

    public function handleGetCommandsAction(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        if (isset($request->getQueryParams()['status'])) {
            switch ($request->getQueryParams()['status']) {
                case 'enabled':
                    $result = $this->commandStatusService->getAvailableCommands();
                    return $this->buildResponse($response, $result);
                case 'disabled':
                    $result = $this->commandStatusService->getDisabledCommand();
                    return $this->buildResponse($response, $result);
                default:
                    $result = Result::from(false, 'Invalid status', null, 400);
                    return $this->buildResponse($response, $result);
            }
        }

        $result = $this->commandStatusService->getAllCommands();
        return $this->buildResponse($response, $result);
    }

    public function handleToggleCommand(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (isset($request->getQueryParams()['name'])) {
            $result = $this->commandStatusService->toggleCommandByName($request->getQueryParams()['name']);
            return $this->buildResponse($response, $result);
        }

        if (isset($request->getQueryParams()['id'])) {
            $result = $this->commandStatusService->toggleCommandById((int)$request->getQueryParams()['id']);
            return $this->buildResponse($response, $result);
        }

        $result = Result::from(false, 'Name or ID is required', null, 400);
        return $this->buildResponse($response, $result);
    }
}