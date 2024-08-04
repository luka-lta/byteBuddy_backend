<?php
declare(strict_types=1);

namespace ByteBuddyApi\V1\Factory;

use Wohali\OAuth2\Client\Provider\Discord;

class OAuthProviderFactory
{
    public function __invoke(): Discord
    {
        return new Discord([
            'clientId'                => '1207338627927904327',
            'clientSecret'            => 'KNpuvhHXfrdaZDz9Ohl5QjKYVCW0DlSF',
            'scopes'                  => ['identify', 'email', 'guilds'],
            'redirectUri'             => 'http://localhost/api/v1/auth/discord/callback',
        ]);
    }
}