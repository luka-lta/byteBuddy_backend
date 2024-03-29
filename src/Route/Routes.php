<?php
declare(strict_types=1);

namespace ByteBuddyApi\Route;

use ByteBuddyApi\Action\Auth\DiscordAuthAction;
use ByteBuddyApi\Action\Birthday\BirthdayAction;
use ByteBuddyApi\Action\Channel\ChannelConfigAction;
use ByteBuddyApi\Action\Command\CommandAction;
use ByteBuddyApi\Action\Guild\GuildAction;
use ByteBuddyApi\Action\Health\HealthAction;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

class Routes
{
    public static function getRoutes(App $app): void
    {
        $app->group('/api/v1', function (RouteCollectorProxy $group) {
            $group->get('/health', [HealthAction::class, 'handleHealthAction']);

            $group->post('/register', [GuildAction::class, 'handleRegisterGuild']);

            $group->get('/guild', [GuildAction::class, 'handleGetConfigAction']);
            $group->post('/guild', [GuildAction::class, 'handleSetConfigData']);

            $group->post('/channels', [ChannelConfigAction::class, 'handleSetChannels']);
            $group->get('/channels', [ChannelConfigAction::class, 'handleGetChannels']);

            $group->get('/birthdays', [BirthdayAction::class, 'handleGetBirthdaysFromGuildAction']);
            $group->post('/birthdays', [BirthdayAction::class, 'handleSetOrUpdateBirthdaysAction']);

            $group->get('/commands', [CommandAction::class, 'handleGetCommandsAction']);
            $group->post('/commands/toggle', [CommandAction::class, 'handleToggleCommand']);
            $group->post('/commands/register', [CommandAction::class, 'handleRegisterCommandAction']);

            $group->get('/auth/discord', [DiscordAuthAction::class, 'handleDiscordAuthAction']);
            $group->get('/auth/discord/callback', [DiscordAuthAction::class, 'handleDiscordCallbackAuthAction']);
        });
    }
}