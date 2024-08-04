<?php
declare(strict_types=1);

namespace ByteBuddyApi\V1\Service\Results;

use ByteBuddyApi\V1\Exception\ByteBuddyException;
use ByteBuddyApi\V1\Repository\BirthdayRepository;
use ByteBuddyApi\V1\Value\BirthdayObject;
use ByteBuddyApi\V1\Value\Result;
use DateTime;
use Exception;

class BirthdayService
{
    public function __construct(
        private readonly BirthdayRepository $birthdayRepository,
    )
    {
    }

    public function getBirthdays(string $guildId): Result
    {
        try {
            $birthdays = $this->birthdayRepository->getAllBirthdays($guildId);
            return Result::from(true, 'Birthdays found', $birthdays, 200);
        } catch (ByteBuddyException $e) {
            return Result::from(false, $e->getMessage(), null, $e->getCode());
        } catch (Exception) {
            return Result::from(false, 'An error occurred', null, 500);
        }
    }

    public function setOrUpdateBirthday(string $guildId, string $userId, string $birthdayDate): Result
    {
        try {
            $birthdayDate = DateTime::createFromFormat('Y-m-d', $birthdayDate);
            $birthdayObject = BirthdayObject::from($guildId, $userId, $birthdayDate);
            $this->birthdayRepository->setOrUpdateBirthday($birthdayObject);

            return Result::from(true, 'Birthday set successfully', null, 200);
        } catch (ByteBuddyException $e) {
            return Result::from(false, $e->getMessage(), null, $e->getCode());
        } catch (Exception) {
            return Result::from(false, 'An error occurred', null, 500);
        }
    }
}