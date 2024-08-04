<?php

declare(strict_types=1);

namespace ByteBuddyApi\V1\Action\User;

use ByteBuddyApi\V1\Action\ByteBuddyAction;
use ByteBuddyApi\V1\Exception\ByteBuddyValidationException;
use ByteBuddyApi\V1\Service\Results\User\RoleService;
use ByteBuddyApi\V1\Service\ValidationService;
use ByteBuddyApi\V1\Value\Result;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class RoleAction extends ByteBuddyAction
{
    public function __construct(
        private readonly RoleService $roleService,
        private readonly ValidationService $validationService,
        private readonly Logger $logger,
    ) {
    }

    public function handleGetRoleFromUserAction(
        ServerRequestInterface $request,
        ResponseInterface $response,
        string $userId
    ): ResponseInterface {
        $result = $this->roleService->getRoleFromUser((int) $userId, $request->getAttribute('decodedToken')['uid']);
        return $this->buildResponse($response, $result);
    }

    public function handleUpdateRoleFromUserAction(
        ServerRequestInterface $request,
        ResponseInterface $response,
        string $userId
    ): ResponseInterface {
        try {
            $this->validationService->checkForRequiredBodyParams(['role'], $request->getParsedBody());
            $result = $this->roleService->updateRoleFromUser(
                (int) $userId,
                $request->getParsedBody()['role'],
                $request->getAttribute('decodedToken')['uid']
            );
        } catch (ByteBuddyValidationException $e) {
            $result = Result::from(false, $e->getMessage(), null, $e->getCode());
        }  catch (Throwable $e) {
            $this->logger->error($e->getMessage());
            $result = Result::from(false, 'Failed to Update role', null, 500);
        }

        return $this->buildResponse($response, $result);
    }
}
