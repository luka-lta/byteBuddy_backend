<?php

declare(strict_types=1);

namespace ByteBuddyApi\Action\Auth;

use ByteBuddyApi\Action\ByteBuddyAction;
use ByteBuddyApi\Exception\ByteBuddyValidationException;
use ByteBuddyApi\Service\Results\User\UserService;
use ByteBuddyApi\Service\ValidationService;
use ByteBuddyApi\Value\Result;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoginAction extends ByteBuddyAction
{
    public function __construct(
        private readonly UserService $userService,
        private readonly ValidationService $validationService
    ) {
    }

    public function handleLogin(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $this->validationService->checkForRequiredBodyParams(['email', 'password'], $request->getParsedBody());
            $result = $this->userService->loginUser(
                $request->getParsedBody()['email'],
                $request->getParsedBody()['password']
            );
        } catch (ByteBuddyValidationException $e) {
            $result = Result::from(false, $e->getMessage(), null, 400);
        }

        return $this->buildResponse($response, $result);
    }
}
