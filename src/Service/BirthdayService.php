<?php
declare(strict_types=1);

namespace ByteBuddyApi\Service;

use ByteBuddyApi\Exception\ByteBuddyException;
use ByteBuddyApi\Repository\BirthdayRepository;
use ByteBuddyApi\Value\BirthdayObject;
use ByteBuddyApi\Value\ResultObject;
use DateTime;
use Exception;

class BirthdayService
{
    public function __construct(
        private readonly BirthdayRepository $birthdayRepository,
    )
    {
    }

    public function getBirthdays(string $guildId): ResultObject
    {
        try {
            $birthdays = $this->birthdayRepository->getAllBirthdays($guildId);
            if (!$birthdays) {
                return ResultObject::from(false, 'No birthdays found', null, 404);
            }

            return ResultObject::from(true, 'Birthdays found', $birthdays, 200);
        } catch (ByteBuddyException $e) {
            return ResultObject::from(false, $e->getMessage(), null, $e->getCode());
        } catch (Exception) {
            return ResultObject::from(false, 'An error occurred', null, 500);
        }
    }

    public function setOrUpdateBirthday(string $guildId, string $userId, string $birthdayDate): ResultObject
    {
        try {
            $birthdayDate = DateTime::createFromFormat('Y-m-d', $birthdayDate);
            $birthdayObject = BirthdayObject::from($guildId, $userId, $birthdayDate);
            $this->birthdayRepository->setOrUpdateBirthday($birthdayObject);

            return ResultObject::from(true, 'Birthday set successfully', null, 200);
        } catch (ByteBuddyException $e) {
            return ResultObject::from(false, $e->getMessage(), null, $e->getCode());
        } catch (Exception) {
            return ResultObject::from(false, 'An error occurred', null, 500);
        }
    }
}