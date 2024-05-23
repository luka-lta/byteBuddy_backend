<?php

declare(strict_types=1);

namespace ByteBuddyApi\Service;

use ByteBuddyApi\Exception\ByteBuddyDatabaseException;
use ByteBuddyApi\Exception\ByteBuddyUserAlreadyExistsException;
use ByteBuddyApi\Exception\ByteBuddyUserNotFoundException;
use ByteBuddyApi\Exception\ByteBuddyValidationException;
use ByteBuddyApi\Repository\UserRepository;
use ByteBuddyApi\Value\User\User;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly JwtService $jwtService,
    ) {
    }

    /**
     * @throws ByteBuddyValidationException
     * @throws ByteBuddyDatabaseException
     * @throws ByteBuddyUserAlreadyExistsException
     */
    public function createUser(string $username, string $email, string $password): User
    {
        if ($this->userExists($email)) {
            throw new ByteBuddyUserAlreadyExistsException(
                'User with email ' . $email . ' already exists',
                400
            );
        }

        if ($this->usernameExists($username)) {
            throw new ByteBuddyUserAlreadyExistsException(
                'User with username ' . $username . ' already exists',
                400
            );
        }

        $user = User::from(null, $username, $email, $password, 'USER');
        $user->generatePasswordFromPlain($password);

        return $this->userRepository->createUser($user);
    }

    /**
     * @throws ByteBuddyValidationException
     * @throws ByteBuddyDatabaseException
     * @throws ByteBuddyUserNotFoundException
     */
    public function loginUser(string $email, string $password): array
    {
        $user = $this->getUserByEmail($email);
        $token = $this->jwtService->generateNewToken($user->getUserId(), $user->getUsername());

        if (!$user->verifyPassword($password)) {
            throw new ByteBuddyValidationException(
                'Invalid password',
                400
            );
        }

        if ($user->isDisabled()) {
            throw new ByteBuddyValidationException(
                'User is disabled',
                400
            );
        }

        return [
            'token' => $token,
            'user' => $user->toArray(),
        ];
    }

    /**
     * @throws ByteBuddyUserNotFoundException
     * @throws ByteBuddyDatabaseException
     */
    public function updateUser(User $user): void
    {
        var_dump($this->userExists($user->getUserId()));

        if (!$this->userExists($user->getUserId())) {
            throw new ByteBuddyUserNotFoundException(
                'User with id ' . $user->getUserId() . ' not found',
                404
            );
        }

        $this->userRepository->updateUser($user);
    }

    /**
     * @throws ByteBuddyValidationException
     * @throws ByteBuddyUserNotFoundException
     * @throws ByteBuddyDatabaseException
     */
    public function changePassword(int $userId, string $newPassword): void
    {
        $user = $this->getUserById($userId);

        if (!$user->verifyPassword($newPassword)) {
            throw new ByteBuddyValidationException(
                'Invalid old password',
                400
            );
        }

        $user->generatePasswordFromPlain($newPassword);
        $this->userRepository->changePassword($userId, $user->getHashedPassword());
    }

    /**
     * @throws ByteBuddyUserNotFoundException
     * @throws ByteBuddyDatabaseException
     */
    public function deleteUser(int $userId): void
    {
        if (!$this->userExists($userId)) {
            throw new ByteBuddyUserNotFoundException(
                'User with id ' . $userId . ' not found',
                404
            );
        }
        $this->userRepository->deleteUser($userId);
    }

    /**
     * @throws ByteBuddyValidationException
     * @throws ByteBuddyDatabaseException
     */
    public function getAllUsers(): array
    {
        return $this->userRepository->getAllUsers();
    }

    /**
     * @throws ByteBuddyValidationException
     * @throws ByteBuddyDatabaseException
     * @throws ByteBuddyUserNotFoundException
     */
    public function getUserById(int $userId): User
    {
        $userData = $this->userRepository->getUserById($userId);
        $this->ensureUserExists($userData, $userId);

        return $this->createUserFromDatabaseData($userData);
    }

    /**
     * @throws ByteBuddyValidationException
     * @throws ByteBuddyDatabaseException
     * @throws ByteBuddyUserNotFoundException
     */
    public function getUserByEmail(string $email): User
    {
        $userData = $this->userRepository->getUserByEmail($email);
        $this->ensureUserExists($userData, $email);

        return $this->createUserFromDatabaseData($userData);
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function userExists(string|int $identifier): bool
    {
        if (is_int($identifier)) {
            return $this->userRepository->userExistsById($identifier);
        }

        return $this->userRepository->userExists($identifier);
    }

    /**
     * @throws ByteBuddyDatabaseException
     */
    public function usernameExists(string $username): bool
    {
        return $this->userRepository->userNameExists($username);
    }

    /**
     * @throws ByteBuddyUserNotFoundException
     */
    private function ensureUserExists(array $userData, int|string $identifier): void
    {
        if (empty($userData)) {
            throw new ByteBuddyUserNotFoundException(
                'User with identifier ' . $identifier . ' not found',
                404
            );
        }
    }

    /**
     * @throws ByteBuddyValidationException
     */
    private function createUserFromDatabaseData(array $userData): User
    {
        return User::fromDatabase($userData);
    }
}
