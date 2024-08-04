<?php

declare(strict_types=1);

namespace ByteBuddyApi\V1\Action\User;

use ByteBuddyApi\V1\Action\ByteBuddyAction;
use ByteBuddyApi\V1\Exception\ByteBuddyValidationException;
use ByteBuddyApi\V1\Service\Results\User\UserActionService;
use ByteBuddyApi\V1\Service\ValidationService;
use ByteBuddyApi\V1\Value\Result;
use ByteBuddyApi\V1\Value\User\User;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class UpdateUserAction extends ByteBuddyAction
{
    public function __construct(
        private readonly UserActionService $userService,
        private readonly ValidationService $validationService,
        private readonly Logger            $logger,
    ) {
    }

    public function handleUpdateUserAction(
        ServerRequestInterface $request,
        ResponseInterface      $response,
        string                 $userId
    ): ResponseInterface {
        try {
            $this->validationService->checkForRequiredBodyParams([
                'username',
                'email',
                'role'
            ], $request->getParsedBody());
            $username = $request->getParsedBody()['username'];
            $email = $request->getParsedBody()['email'];
            $role = $request->getParsedBody()['role'];

            $updatedUser = User::from((int)$userId, $username, $email, null, $role);
            $result = $this->userService->updateUser($updatedUser, $request->getAttribute('decodedToken')['uid']);
        } catch (ByteBuddyValidationException $e) {
            $result = Result::from(false, $e->getMessage(), null, 400);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
            $result = Result::from(false, 'Failed to update user', null, 500);
        }

        return $this->buildResponse($response, $result);
    }

    public function handleChangePasswordAction(
        ServerRequestInterface $request,
        ResponseInterface      $response,
        string                 $userId
    ): ResponseInterface {
        try {
            $this->validationService->checkForRequiredBodyParams([
                'oldPassword',
                'newPassword'
            ], $request->getParsedBody());
            $oldPassword = $request->getParsedBody()['oldPassword'];
            $newPassword = $request->getParsedBody()['newPassword'];

            $result = $this->userService->changePassword(
                (int)$userId,
                $oldPassword,
                $newPassword,
                $request->getAttribute('decodedToken')['uid']
            );
        } catch (ByteBuddyValidationException $e) {
            $result = Result::from(false, $e->getMessage(), null, 400);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
            $result = Result::from(false, 'Failed to change password', null, 500);
        }

        return $this->buildResponse($response, $result);
    }
}
