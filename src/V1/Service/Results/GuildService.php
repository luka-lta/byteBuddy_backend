<?php

declare(strict_types=1);

namespace ByteBuddyApi\V1\Service\Results;

use ByteBuddyApi\V1\Exception\ByteBuddyException;
use ByteBuddyApi\V1\Repository\GuildRepository;
use ByteBuddyApi\V1\Value\Result;
use Exception;

class GuildService
{
    public function __construct(private readonly GuildRepository $configRepository)
    {
    }

    public function getAllGuilds(): Result
    {
        try {
            $guilds = $this->configRepository->getAllGuilds();
        } catch (ByteBuddyException $e) {
            return Result::from(false, $e->getMessage(), null, $e->getCode());
        } catch (Exception) {
            return Result::from(false, 'An error occurred', null, 500);
        }

        return Result::from(true, 'Guilds fetched successfully', $guilds, 200);
    }

    public function registerGuild(string $guildId, string $serverName): Result
    {
        try {
            $this->configRepository->registerNewGuild($guildId, $serverName);
        } catch (ByteBuddyException $e) {
            return Result::from(false, $e->getMessage(), null, $e->getCode());
        } catch (Exception) {
            return Result::from(false, 'An error occurred', null, 500);
        }

        return Result::from(true, 'Config data fetched successfully', null, 200);
    }

    public function getConfigData(string $guildId): Result
    {
        try {
            $configData = $this->configRepository->getConfigData($guildId);
        } catch (ByteBuddyException $e) {
            return Result::from(false, $e->getMessage(), null, $e->getCode());
        } catch (Exception) {
            return Result::from(false, 'An error occurred', null, 500);
        }

        return Result::from(true, 'Config data fetched successfully', $configData->asArray(), 200);
    }

    public function setConfigValue(string $guildId, array $changedValues): Result
    {
        try {
            foreach ($changedValues as $key => $value) {
                $this->configRepository->setConfigKey($guildId, $key, $value);
            }
        } catch (ByteBuddyException $e) {
            return Result::from(false, $e->getMessage(), null, $e->getCode());
        } catch (Exception) {
            return Result::from(false, 'An error occurred', null, 500);
        }

        return Result::from(true, 'Config data updated successfully', null, 200);
    }
}
