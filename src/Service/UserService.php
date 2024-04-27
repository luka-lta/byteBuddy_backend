<?php

declare(strict_types=1);

namespace ByteBuddyApi\Service;

use ByteBuddyApi\Exception\ByteBuddyException;
use ByteBuddyApi\Repository\UserRepository;
use ByteBuddyApi\Value\Result;
use ByteBuddyApi\Value\User\User;
use Exception;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly JwtService $jwtService,
        private readonly AccessService $accessService,
    ) {
    }

    // TODO: Add validation
    public function registerUser(string $username, string $email, string $password): Result
    {
        $user = User::from(null, $username, $email, $password, ['USER']);
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

    public function getUserById(int $userId, string $token): Result
    {
        try {
            $user = $this->userRepository->findUserById($userId);

            if (!$this->accessService->hasAccess($userId, $token)) {
                return Result::from(false, 'Unauthorized access', null, 403);
            }
        } catch (ByteBuddyException $e) {
            return Result::from(false, $e->getMessage(), null, $e->getCode());
        }

        return Result::from(true, 'User found', $user->toArray(), 200);
    }
}
