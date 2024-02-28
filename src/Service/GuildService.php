<?php
declare(strict_types=1);

namespace ByteBuddyApi\Service;

use ByteBuddyApi\Exception\ByteBuddyException;
use ByteBuddyApi\Repository\GuildRepository;
use ByteBuddyApi\Value\ResultObject;
use Exception;
use Monolog\Logger;

class GuildService
{
    public function __construct(
        private readonly GuildRepository $configRepository,
        private readonly Logger $logger,
    )
    {
    }

    public function registerGuild(string $guildId, string $serverName): ResultObject
    {
        try {
            $this->configRepository->registerNewGuild($guildId, $serverName);
        } catch (ByteBuddyException $e) {
            return ResultObject::from(false, $e->getMessage(), null, $e->getCode());
        } catch (Exception) {
            return ResultObject::from(false, 'An error occurred', null, 500);
        }

        return ResultObject::from(
            true,
            'Config data fetched successfully',
            null,
            200
        );
    }

    public function getConfigData(string $guildId): ResultObject
    {
        try {
            $configData = $this->configRepository->getConfigData($guildId);
        } catch (ByteBuddyException $e) {
            return ResultObject::from(false, $e->getMessage(), null, $e->getCode());
        } catch (Exception) {
            return ResultObject::from(false, 'An error occurred', null, 500);
        }

        return ResultObject::from(
            true,
            'Config data fetched successfully',
            $configData->asArray(),
            200
        );
    }

    public function setConfigValue(string $guildId, array $changedValues): ResultObject
    {
        try {
            foreach ($changedValues as $key => $value) {
                if (!$this->configRepository->setConfigKey($guildId, $key, $value)) {
                    return ResultObject::from(false, 'Failed to update config data', null, 500);
                }
            }
        } catch (ByteBuddyException $e) {
            return ResultObject::from(false, $e->getMessage(), null, $e->getCode());
        } catch (Exception) {
            return ResultObject::from(false, 'An error occurred', null, 500);
        }

        return ResultObject::from(true, 'Config data updated successfully', null, 200);
    }
}