<?php
declare(strict_types=1);

namespace ByteBuddyApi\Route;

use ByteBuddyApi\Action\Birthday\BirthdayAction;
use ByteBuddyApi\Action\Channel\ChannelConfigAction;
use ByteBuddyApi\Action\Command\CommandStatusAction;
use ByteBuddyApi\Action\Guild\GuildAction;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

class Routes
{
    public static function getRoutes(App $app): void
    {
        $app->group('/api/v1', function (RouteCollectorProxy $group) {
            $group->post('/register', [GuildAction::class, 'handleRegisterGuild']);

            $group->get('/guild', [GuildAction::class, 'handleGetConfigAction']);
            $group->post('/guild', [GuildAction::class, 'handleSetConfigData']);

            $group->post('/channels', [ChannelConfigAction::class, 'handleSetChannels']);
            $group->get('/channels', [ChannelConfigAction::class, 'handleGetChannels']);

            $group->get('/birthdays', [BirthdayAction::class, 'handleGetBirthdaysFromGuildAction']);
            $group->post('/birthdays', [BirthdayAction::class, 'handleSetOrUpdateBirthdaysAction']);

            $group->get('/commands/disabled', [CommandStatusAction::class, 'handleGetDisabledCommandsAction']);
            $group->post('/commands/enable', [CommandStatusAction::class, 'handleEnableCommandAction']);
            $group->post('/commands/disable', [CommandStatusAction::class, 'handleDisableCommandAction']);
        });
    }
}