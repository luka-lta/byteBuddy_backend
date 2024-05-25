<?php

declare(strict_types=1);

namespace ByteBuddyApi\Action\History;

use ByteBuddyApi\Action\ByteBuddyAction;
use ByteBuddyApi\Service\Results\CommandHistoryActionService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CommandHistoryAction extends ByteBuddyAction
{
    public function __construct(
        private readonly CommandHistoryActionService $commandHistoryActionService,
    ) {
    }

    public function handleCreateCommandHistory(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $userId = (int)$request->getParsedBody()['userId'];
        $guildId = (int)$request->getParsedBody()['guildId'];
        $command = $request->getParsedBody()['commandName'];

        $result = $this->commandHistoryActionService->createHistory($userId, $guildId, $command);

        return $this->buildResponse($response, $result);
    }
}
