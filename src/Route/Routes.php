<?php
declare(strict_types=1);

namespace ByteBuddyApi\Route;

use ByteBuddyApi\Action\Birthday\BirthdayAction;
use ByteBuddyApi\Action\Channel\ChannelConfigAction;
use ByteBuddyApi\Action\Config\ConfigAction;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

class Routes
{
    public static function getRoutes(App $app): void
    {
        $app->group('/api/v1', function (RouteCollectorProxy $group) {
            $group->post('/register', [ConfigAction::class, 'handleRegisterGuild']);

            $group->get('/config', [ConfigAction::class, 'handleGetConfigAction']);
            $group->post('/config', [ConfigAction::class, 'handleSetConfigData']);

            $group->post('/channels', [ChannelConfigAction::class, 'handleSetChannels']);
            $group->get('/channels', [ChannelConfigAction::class, 'handleGetChannels']);

            $group->get('/birthdays', [BirthdayAction::class, 'handleGetBirthdaysFromGuildAction']);
            $group->post('/birthdays', [BirthdayAction::class, 'handleSetOrUpdateBirthdaysAction']);
        });
    }
}