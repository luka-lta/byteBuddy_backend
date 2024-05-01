<?php
declare(strict_types=1);

namespace ByteBuddyApi\Route;

use ByteBuddyApi\Action\Auth\LoginAction;
use ByteBuddyApi\Action\Auth\RegisterAction;
use ByteBuddyApi\Action\Birthday\BirthdayAction;
use ByteBuddyApi\Action\Channel\ChannelConfigAction;
use ByteBuddyApi\Action\Command\CommandAction;
use ByteBuddyApi\Action\Guild\GuildAction;
use ByteBuddyApi\Action\Health\HealthAction;
use ByteBuddyApi\Action\User\DeleteUserAction;
use ByteBuddyApi\Action\User\GetUserAction;
use ByteBuddyApi\Action\User\RoleAction;
use ByteBuddyApi\Action\User\UpdateUserAction;
use ByteBuddyApi\Middleware\AuthMiddleware;
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

            $group->group('/user', function (RouteCollectorProxy $user) {
                $user->post('/register', [RegisterAction::class, 'handleRegisterNewUser']);
                $user->post('/login', [LoginAction::class, 'handleLogin']);

                // Get User
                $user->get('/{userId:[0-9]+}', [GetUserAction::class, 'handleGetUserAction'])->add(AuthMiddleware::class);
                $user->get('/all', [GetUserAction::class, 'handleGetAllUserAction']);
                $user->get('/roles/{userId:[0-9]+}', [RoleAction::class, 'handleGetRoleFromUserAction'])->add(AuthMiddleware::class);

                // Update User
                $user->put('/{userId:[0-9]+}', [UpdateUserAction::class, 'handleUpdateUserAction'])->add(AuthMiddleware::class);
                $user->put('/changePassword/{userId:[0-9]+}', [UpdateUserAction::class, 'handleChangePasswordAction'])->add(AuthMiddleware::class);
                $user->put('/roles/{userId:[0-9]+}', [RoleAction::class, 'handleUpdateRoleFromUserAction'])->add(AuthMiddleware::class);

                // Delete User
                $user->delete('/{id:[0-9]+}', [DeleteUserAction::class, 'handleDeleteUserAction']);
            });
        });
    }
}