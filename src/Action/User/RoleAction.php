<?php

declare(strict_types=1);

namespace ByteBuddyApi\Action\User;

use ByteBuddyApi\Action\ByteBuddyAction;
use ByteBuddyApi\Exception\ByteBuddyValidationException;
use ByteBuddyApi\Service\Results\User\RoleService;
use ByteBuddyApi\Service\ValidationService;
use ByteBuddyApi\Value\Result;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RoleAction extends ByteBuddyAction
{
    public function __construct(
        private readonly RoleService $roleService,
        private readonly ValidationService $validationService,
    )
    {
    }

    public function handleGetRoleFromUserAction(
        ServerRequestInterface $request,
        ResponseInterface $response,
        string $userId
    ): ResponseInterface {
        $result = $this->roleService->getRoleFromUser((int) $userId);
        return $this->buildResponse($response, $result);
    }

    // TODO: Add authentication
    public function handleUpdateRoleFromUserAction(
        ServerRequestInterface $request,
        ResponseInterface $response,
        string $userId
    ): ResponseInterface {
        try {
            $this->validationService->checkForRequiredBodyParams(['role'], $request->getParsedBody());
            $result = $this->roleService->updateRoleFromUser((int) $userId, $request->getParsedBody()['role']);
        } catch (ByteBuddyValidationException $e) {
            $result = Result::from(false, $e->getMessage(), null, $e->getCode());
        }

        return $this->buildResponse($response, $result);
    }
}
