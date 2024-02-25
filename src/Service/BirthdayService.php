<?php
declare(strict_types=1);

namespace ByteBuddyApi\Service;

use ByteBuddyApi\Exception\ByteBuddyException;
use ByteBuddyApi\Repository\BirthdayRepository;
use ByteBuddyApi\Value\BirthdayObject;
use ByteBuddyApi\Value\ResultObject;
use DateTime;

class BirthdayService
{
    public function __construct(
        private readonly BirthdayRepository $birthdayRepository
    )
    {
    }

    public function getBirthdays(int $guildId): ResultObject
    {
        if ($guildId == null) {
            return ResultObject::from(false, 'GuildId must be set', null, 400);
        }

        try {
            $birthdays = $this->birthdayRepository->getAllBirthdays($guildId);
            if (!$birthdays) {
                return ResultObject::from(false, 'No birthdays found', null, 404);
            }

            return ResultObject::from(true, 'Birthdays found', $birthdays, 200);
        } catch (ByteBuddyException $e) {
            return ResultObject::from(false, $e->getMessage(), null, $e->getCode());
        }
    }

    public function setOrUpdateBirthday(int|null $guildId, int|null $userId, DateTime|null $birthdayDate): ResultObject
    {
        if ($guildId == null) {
            return ResultObject::from(false, 'GuildId must be set', null, 400);
        }

        if ($userId == null) {
            return ResultObject::from(false, 'GuildId must be set', null, 400);
        }

        if ($birthdayDate == null) {
            return ResultObject::from(false, 'Birthday date must be set', null, 400);
        }

        try {
            $birthdayObject = BirthdayObject::from($guildId, $userId, $birthdayDate);
            $this->birthdayRepository->setOrUpdateBirthday($birthdayObject);
            return ResultObject::from(true, 'Birthday set successfully', null, 200);
        } catch (ByteBuddyException $e) {
            return ResultObject::from(false, $e->getMessage(), null, $e->getCode());
        }
    }
}