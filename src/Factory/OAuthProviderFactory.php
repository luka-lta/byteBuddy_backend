<?php
declare(strict_types=1);

namespace ByteBuddyApi\Factory;

use League\OAuth2\Client\Provider\GenericProvider;

class OAuthProviderFactory
{
    public function __invoke(): GenericProvider
    {
        return new GenericProvider([
            'clientId'                => '1207338627927904327',
            'clientSecret'            => 'KNpuvhHXfrdaZDz9Ohl5QjKYVCW0DlSF',
            'redirectUri'             => 'http://localhost/auth/discord/callback',
            'urlAuthorize'            => 'https://discord.com/api/oauth2/authorize',
            'urlAccessToken'         => 'https://discord.com/api/oauth2/token',
            'urlResourceOwnerDetails' => 'https://discord.com/api/users/@me',
        ]);
    }
}