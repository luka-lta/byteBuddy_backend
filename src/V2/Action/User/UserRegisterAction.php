<?php

declare(strict_types=1);

namespace ByteBuddyApi\V2\Action\User;

use ByteBuddyApi\V2\Action\ByteBuddyAction;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserRegisterAction extends ByteBuddyAction
{
    public function __construct(private readonly UserService $userService)
    {
    }

    protected function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userData = $request->getParsedBody();
        $result = $this->userService->createUser($userData);

        return $result->getResponse($response);
    }
}
