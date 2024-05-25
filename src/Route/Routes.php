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
use ByteBuddyApi\Action\History\CommandHistoryAction;
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
            $group->get('/guilds', [GuildAction::class, 'handleGetAllGuilds']);
            $group->post('/channels', [ChannelConfigAction::class, 'handleSetChannels']);
            $group->get('/channels', [ChannelConfigAction::class, 'handleGetChannels']);
            $group->get('/birthdays', [BirthdayAction::class, 'handleGetBirthdaysFromGuildAction']);
            $group->post('/birthdays', [BirthdayAction::class, 'handleSetOrUpdateBirthdaysAction']);

            $group->group('/commands', function (RouteCollectorProxy $commands) {
                $commands->get('', [CommandAction::class, 'handleGetCommandsAction']);
                $commands->post('/toggle', [CommandAction::class, 'handleToggleCommand']);
                $commands->post('/register', [CommandAction::class, 'handleRegisterCommandAction']);
            });


            // TODO: Auth for bot
            $group->group('/history', function (RouteCollectorProxy $history) {
                $history->post('/command', [CommandHistoryAction::class, 'handleCreateCommandHistory']);
            });

            $group->group('/user', function (RouteCollectorProxy $user) {

                $user->post('/register', [RegisterAction::class, 'handleRegisterNewUser']);
                $user->post('/login', [LoginAction::class, 'handleLogin']);

                // Route zum Ändern des Passworts für einen Benutzer
                $user->put(
                    '/changePassword/{userId:[0-9]+}',
                    [UpdateUserAction::class, 'handleChangePasswordAction']
                )->add(AuthMiddleware::class);

                // Route, um alle Benutzer abzurufen
                $user->get('/all', [GetUserAction::class, 'handleGetAllUserAction']);

                // Gruppe für spezifische Benutzer-IDs
                $user->group('/{userId:[0-9]+}', function (RouteCollectorProxy $modifyUser) {
                    $modifyUser->get('', [GetUserAction::class, 'handleGetUserAction']);
                    $modifyUser->put('', [UpdateUserAction::class, 'handleUpdateUserAction']);
                    $modifyUser->delete('', [DeleteUserAction::class, 'handleDeleteUserAction']);
                })->add(AuthMiddleware::class);

                // Gruppe für Benutzerrollen
                $user->group('/roles', function (RouteCollectorProxy $roles) {
                    $roles->get('/{userId:[0-9]+}', [RoleAction::class, 'handleGetRoleFromUserAction']);
                    $roles->put('/{userId:[0-9]+}', [RoleAction::class, 'handleUpdateRoleFromUserAction']);
                })->add(AuthMiddleware::class);
            });
        });
    }
}
