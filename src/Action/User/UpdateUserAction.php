<?php

declare(strict_types=1);

namespace ByteBuddyApi\Action\User;

use ByteBuddyApi\Action\ByteBuddyAction;
use ByteBuddyApi\Service\UserService;
use ByteBuddyApi\Value\User\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UpdateUserAction extends ByteBuddyAction
{
    public function __construct(
        private readonly UserService $userService
    )
    {
    }

    public function handleUpdateUserAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userId = (int)$request->getParsedBody()['userId'];
        $username = $request->getParsedBody()['username'];
        $email = $request->getParsedBody()['email'];
        $password = $request->getParsedBody()['password'];;
        $roles = $request->getParsedBody()['roles'];

        $updatedUser = User::from($userId, $username, $email, $password, $roles);
        $updatedUser->generatePasswordFromPlain($password);

        $result = $this->userService->updateUser($updatedUser, $request->getHeader('Authorization')[0]);
        return $this->buildResponse($response, $result);
    }
}
