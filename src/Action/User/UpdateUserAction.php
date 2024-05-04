<?php

declare(strict_types=1);

namespace ByteBuddyApi\Action\User;

use ByteBuddyApi\Action\ByteBuddyAction;
use ByteBuddyApi\Service\Results\User\UserService;
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

    public function handleUpdateUserAction(
        ServerRequestInterface $request,
        ResponseInterface $response,
        string $userId
    ): ResponseInterface {
        $userId = (int)$userId;
        $username = $request->getParsedBody()['username'];
        $email = $request->getParsedBody()['email'];
        $password = $request->getParsedBody()['password']?? null;
        $role = $request->getParsedBody()['role'];

        $updatedUser = User::from($userId, $username, $email, $password, $role);

        if ($password) {
            $updatedUser->generatePasswordFromPlain($password);
        }

        $result = $this->userService->updateUser($updatedUser, $request->getHeader('Authorization')[0]);
        return $this->buildResponse($response, $result);
    }

    public function handleChangePasswordAction(
        ServerRequestInterface $request,
        ResponseInterface $response,
        string $userId
    ): ResponseInterface {
        $userId = (int)$userId;
        $oldPassword = $request->getParsedBody()['oldPassword'];
        $newPassword = $request->getParsedBody()['newPassword'];

        $result = $this->userService->changePassword($userId, $oldPassword, $newPassword, $request->getHeader('Authorization')[0]);

        return $this->buildResponse($response, $result);
    }
}
