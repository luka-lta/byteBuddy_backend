<?php

declare(strict_types=1);

namespace ByteBuddyApi\Action\Auth;

use ByteBuddyApi\Action\ByteBuddyAction;
use ByteBuddyApi\Service\UserService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoginAction extends ByteBuddyAction
{
public function __construct(
        private readonly UserService $userService,
    )
    {
    }

    public function handleLogin(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $result = $this->userService->loginUser(
            $request->getParsedBody()['email'] ?? null,
            $request->getParsedBody()['password'] ?? null
        );

        return $this->buildResponse($response, $result);
    }
}
