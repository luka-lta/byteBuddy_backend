<?php

declare(strict_types=1);

namespace ByteBuddyApi\V2\Action\User;

use ByteBuddyApi\V1\Exception\ByteBuddyDatabaseException;
use ByteBuddyApi\V1\Exception\ByteBuddyValidationException;
use ByteBuddyApi\V2\Value\CommonResult;
use ByteBuddyApi\V2\Value\GeneralResult;
use ByteBuddyApi\V2\Value\User\User;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    /**
     * @throws ByteBuddyValidationException
     * @throws ByteBuddyDatabaseException
     */
    public function createUser(array $userData): GeneralResult
    {
        $user = User::create(
            $userData['username'],
            $userData['email'],
            $userData['password'],
        );
        $this->userRepository->create($user);

        return GeneralResult::from(
            CommonResult::from('User created successfully!'),
            201
        );
    }
}
