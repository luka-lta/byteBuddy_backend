<?php
declare(strict_types=1);

namespace ByteBuddyApi\Service;

use ByteBuddyApi\Exception\ByteBuddyException;
use ByteBuddyApi\Repository\ConfigRepository;
use ByteBuddyApi\Value\ResultObject;

class ConfigService
{
    public function __construct(
        private readonly ConfigRepository $configRepository
    )
    {
    }

    public function registerGuild(int|null $guildId, string $serverName): ResultObject
    {
        if ($guildId == null) {
            return ResultObject::from(false, 'GuildId must be set', null, 400);
        }

        if ($serverName == null) {
            return ResultObject::from(false, 'Servername must be set', null, 400);
        }

        try {
            $this->configRepository->registerNewGuild($guildId, $serverName);
        } catch (ByteBuddyException $e) {
            return ResultObject::from(false, $e->getMessage(), null, $e->getCode());
        }

        return ResultObject::from(
            true,
            'Config data fetched successfully',
            null,
            200
        );
    }

    public function getConfigData(int|null $guildId): ResultObject
    {
        if ($guildId == null) {
            return ResultObject::from(false, 'GuildId must be set', null, 400);
        }

        try {
            $configData = $this->configRepository->getConfigData($guildId);
        } catch (ByteBuddyException $e) {
            return ResultObject::from(false, $e->getMessage(), null, $e->getCode());
        }

        return ResultObject::from(
            true,
            'Config data fetched successfully',
            $configData->asArray(),
            200
        );
    }

    public function setConfigValue(int|null $guildId, array $changedValues): ResultObject
    {
        if ($guildId == null) {
            return ResultObject::from(false, 'GuildId must be set', null, 400);
        }

        try {
            foreach ($changedValues as $key => $value) {
                if (!$this->configRepository->setConfigKey($guildId, $key, $value)) {
                    return ResultObject::from(false, 'Failed to update config data', null, 500);
                }
            }
        } catch (ByteBuddyException $e) {
            return ResultObject::from(false, $e->getMessage(), null, $e->getCode());
        }

        return ResultObject::from(true, 'Config data updated successfully', null, 200);
    }
}