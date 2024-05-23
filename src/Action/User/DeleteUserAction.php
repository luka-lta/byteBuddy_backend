<?php

declare(strict_types=1);

namespace ByteBuddyApi\Action\User;

use ByteBuddyApi\Action\ByteBuddyAction;
use ByteBuddyApi\Service\Results\User\UserActionService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DeleteUserAction extends ByteBuddyAction
{
    public function __construct(
        private readonly UserActionService $userService,
    ) {
    }

    public function handleDeleteUserAction(
        ServerRequestInterface $request,
        ResponseInterface $response,
        string $userId
    ): ResponseInterface {
        $result = $this->userService->deleteUser((int)$userId, $request->getAttribute('decodedToken')['uid']);
        return $this->buildResponse($response, $result);
    }
}
