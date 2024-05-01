<?php

declare(strict_types=1);

namespace ByteBuddyApi\Action\User;

use ByteBuddyApi\Action\ByteBuddyAction;
use ByteBuddyApi\Service\Results\User\UserService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetUserAction extends ByteBuddyAction
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    public function handleGetUserAction(
        ServerRequestInterface $request,
        ResponseInterface $response,
        string $userId
    ): ResponseInterface {
        $result = $this->userService->getUserById((int)$userId, $request->getHeaderLine('Authorization'));

        return $this->buildResponse($response, $result);
    }

    // TODO: Add pagination
    public function handleGetAllUserAction(ResponseInterface $response): ResponseInterface
    {
        $result = $this->userService->getAllUsers();

        return $this->buildResponse($response, $result);
    }
}
