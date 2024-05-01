<?php

declare(strict_types=1);

namespace ByteBuddyApi\Action\Auth;

use ByteBuddyApi\Action\ByteBuddyAction;
use ByteBuddyApi\Service\Results\User\UserService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RegisterAction extends ByteBuddyAction
{
    public function __construct(
        private readonly UserService $userService,
    )
    {
    }

    public function handleRegisterNewUser(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $result = $this->userService->registerUser(
            $request->getParsedBody()['username'] ?? null,
            $request->getParsedBody()['email'] ?? null,
            $request->getParsedBody()['password'] ?? null
        );

        return $this->buildResponse($response, $result);
    }
}
