<?php

declare(strict_types=1);

namespace ByteBuddyApi\V1\Action\Auth;

use ByteBuddyApi\V1\Action\ByteBuddyAction;
use ByteBuddyApi\V1\Exception\ByteBuddyValidationException;
use ByteBuddyApi\V1\Service\Results\User\UserActionService;
use ByteBuddyApi\V1\Service\ValidationService;
use ByteBuddyApi\V1\Value\Result;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class LoginAction extends ByteBuddyAction
{
    public function __construct(
        private readonly UserActionService $userService,
        private readonly ValidationService $validationService,
        private readonly Logger            $logger,
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
            $this->logger->error($e->getMessage(), $e->getAdditionalData());
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
            $result = Result::from(false, 'Failed to login user', null, 500);
        }

        return $this->buildResponse($response, $result);
    }
}
