<?php

declare(strict_types=1);

namespace ByteBuddyApi\Service\Results\User;

use ByteBuddyApi\Exception\ByteBuddyException;
use ByteBuddyApi\Exception\ByteBuddyValidationException;
use ByteBuddyApi\Repository\UserRepository;
use ByteBuddyApi\Service\AccessService;
use ByteBuddyApi\Service\ExceptionService;
use ByteBuddyApi\Service\UserService;
use ByteBuddyApi\Value\Result;
use ByteBuddyApi\Value\User\User;

class UserActionService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserService $userService,
        private readonly AccessService $accessService,
        private readonly ExceptionService $exceptionService,
    ) {
    }

    /**
     * @throws ByteBuddyValidationException
     */
    public function registerUser(string $username, string $email, string $password): Result
    {
        try {
            $this->userService->createUser($username, $email, $password);
        } catch (ByteBuddyException $e) {
            $this->exceptionService->handleUserException($e);
            return Result::from(false, $e->getMessage(), null, $e->getCode());
        }

        return Result::from(true, 'User registered successfully', null, 201);
    }

    public function loginUser(string $email, string $password): Result
    {
        try {
            $result = $this->userService->loginUser($email, $password);
        } catch (ByteBuddyException $e) {
            $this->exceptionService->handleUserException($e);
            return Result::from(false, $e->getMessage(), null, $e->getCode());
        }

        return Result::from(true, 'User logged in successfully', [
            'token' => $result['token'],
            'user' => $result['user'],
        ], 200);
    }

    public function getUserById(int $userId, int $requestUser): Result
    {
        try {
            $user = $this->userService->getUserById($userId);

            if (!$this->accessService->hasAccess($userId, $requestUser)) {
                return Result::from(false, 'Unauthorized access', null, 401);
            }
        } catch (ByteBuddyException $e) {
            $this->exceptionService->handleUserException($e);
            return Result::from(false, $e->getMessage(), null, $e->getCode());
        }

        return Result::from(true, 'User found', $user->toArray(), 200);
    }

    public function getAllUsers(): Result
    {
        try {
            $users = $this->userService->getAllUsers();
        } catch (ByteBuddyException $e) {
            $this->exceptionService->handleUserException($e);
            return Result::from(false, $e->getMessage(), null, $e->getCode());
        }

        return Result::from(true, 'Users found', $users, 200);
    }

    public function updateUser(User $user, int $accessUserId): Result
    {
        try {
            if (!$this->accessService->hasAccess($user->getUserId(), $accessUserId)) {
                return Result::from(false, 'Forbidden', null, 403);
            }

            $this->userService->updateUser($user);
        } catch (ByteBuddyException $e) {
            $this->exceptionService->handleUserException($e);
            return Result::from(false, $e->getMessage(), null, $e->getCode());
        }

        return Result::from(true, 'User updated successfully', null, 200);
    }

    public function changePassword(int $userId, string $newPassword, int $accessUserId): Result
    {
        try {
            if (!$this->accessService->hasAccess($userId, $accessUserId)) {
                return Result::from(false, 'Forbidden', null, 403);
            }

            $this->userService->changePassword($userId, $newPassword);
        } catch (ByteBuddyException $e) {
            $this->exceptionService->handleUserException($e);
            return Result::from(false, $e->getMessage(), null, $e->getCode());
        }

        return Result::from(true, 'Password changed', null, 200);
    }

    public function deleteUser(int $userId, int $accessUserId): Result
    {
        try {
            if (!$this->accessService->hasAccess($userId, $accessUserId)) {
                return Result::from(false, 'Forbidden', null, 403);
            }

            $this->userRepository->deleteUser($userId);
        } catch (ByteBuddyException $e) {
            $this->exceptionService->handleUserException($e);
            return Result::from(false, $e->getMessage(), null, $e->getCode());
        }

        return Result::from(true, 'User deleted', null, 200);
    }
}
