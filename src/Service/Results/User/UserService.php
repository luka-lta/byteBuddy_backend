<?php

declare(strict_types=1);

namespace ByteBuddyApi\Service\Results\User;

use ByteBuddyApi\Exception\ByteBuddyException;
use ByteBuddyApi\Exception\ByteBuddyValidationException;
use ByteBuddyApi\Repository\UserRepository;
use ByteBuddyApi\Service\AccessService;
use ByteBuddyApi\Service\JwtService;
use ByteBuddyApi\Value\Result;
use ByteBuddyApi\Value\User\User;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly JwtService $jwtService,
        private readonly AccessService $accessService,
    ) {
    }

    // TODO: Add validation
    /**
     * @throws ByteBuddyValidationException
     */
    public function registerUser(string $username, string $email, string $password): Result
    {
        $user = User::from(null, $username, $email, $password, 'USER');
        $user->generatePasswordFromPlain($password);

        try {
            $this->userRepository->createUser($user);
        } catch (ByteBuddyException $e) {
            return Result::from(false, $e->getMessage(), null, $e->getCode());
        }

        return Result::from(true, 'User registered successfully', null, 201);
    }

    public function loginUser(string $email, string $password): Result
    {
        try {
            $user = $this->userRepository->findUserByEmail($email);

            $token = $this->jwtService->generateNewToken($user->getUserId(), $user->getUsername());

            if (!$user->verifyPassword($password)) {
                return Result::from(false, 'Invalid password', null, 401);
            }
        } catch (ByteBuddyException $e) {
            return Result::from(false, $e->getMessage(), null, $e->getCode());
        }

        return Result::from(true, 'User logged in successfully', [
            'token' => $token,
            'user' => $user->toArray(),
        ], 200);
    }

    public function getUserById(int $userId, int $requestUser): Result
    {
        try {
            $user = $this->userRepository->findUserById($userId);

            if (!$this->accessService->hasAccess($userId, $requestUser)) {
                return Result::from(false, 'Unauthorized access', null, 401);
            }
        } catch (ByteBuddyException $e) {
            return Result::from(false, $e->getMessage(), null, $e->getCode());
        }

        return Result::from(true, 'User found', $user->toArray(), 200);
    }

    // TODO: Add only frontend access
    public function getAllUsers(): Result
    {
        try {
            $users = $this->userRepository->getAllUsers();
        } catch (ByteBuddyException $e) {
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

            $this->userRepository->updateUser($user);
        } catch (ByteBuddyException $e) {
            return Result::from(false, $e->getMessage(), null, $e->getCode());
        }

        return Result::from(true, 'User updated successfully', null, 200);
    }

    public function changePassword(int $userId, string $newPassword, string $oldPassword, int $accessUserId): Result
    {
        try {
            if (!$this->accessService->hasAccess($userId, $accessUserId)) {
                return Result::from(false, 'Forbidden', null, 403);
            }

            $user = $this->userRepository->findUserById($userId);

            if ($user->verifyPassword($oldPassword) === false) {
                return Result::from(false, 'Invalid old password', null, 401);
            }

            if ($oldPassword === $newPassword) {
                return Result::from(false, 'New password must be different from old password', null, 400);
            }

            $this->userRepository->changePassword($userId, $newPassword);
        } catch (ByteBuddyException $e) {
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
            return Result::from(false, $e->getMessage(), null, $e->getCode());
        }

        return Result::from(true, 'User deleted', null, 200);
    }
}
