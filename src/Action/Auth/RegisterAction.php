<?php

declare(strict_types=1);

namespace ByteBuddyApi\Action\Auth;

use ByteBuddyApi\Action\ByteBuddyAction;
use ByteBuddyApi\Exception\ByteBuddyValidationException;
use ByteBuddyApi\Service\Results\User\UserService;
use ByteBuddyApi\Service\ValidationService;
use ByteBuddyApi\Value\Result;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class RegisterAction extends ByteBuddyAction
{
    public function __construct(
        private readonly UserService $userService,
        private readonly ValidationService $validationService,
        private readonly Logger $logger,
    ) {
    }

    public function handleRegisterNewUser(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        try {
            $this->validationService->checkForRequiredBodyParams(
                ['username', 'email', 'password'],
                $request->getParsedBody()
            );
            $result = $this->userService->registerUser(
                $request->getParsedBody()['username'],
                $request->getParsedBody()['email'],
                $request->getParsedBody()['password']
            );
        } catch (ByteBuddyValidationException $e) {
            $result = Result::from(false, $e->getMessage(), null, 400);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
            $result = Result::from(false, 'Failed to register user', null, 500);
        }

        return $this->buildResponse($response, $result);
    }
}
