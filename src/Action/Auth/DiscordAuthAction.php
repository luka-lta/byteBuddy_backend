<?php
declare(strict_types=1);

namespace ByteBuddyApi\Action\Auth;

use League\OAuth2\Client\Provider\GenericProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DiscordAuthAction
{
    public function __construct(
        private readonly GenericProvider $provider
    )
    {
    }

    public function handleDiscordAuthAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $authUrl = $this->provider->getAuthorizationUrl();

        return $response->withHeader('Location', $authUrl)->withStatus(302);
    }

    public function handleDiscordCallbackAuthAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $code = $request->getQueryParams()['code'] ?? null;

        // Access Token und Refresh Token aus dem Code abrufen
        $token = $this->provider->getAccessToken($code);

        // Benutzerdaten aus dem Access Token abrufen
        $user = $this->provider->getResourceOwner($token);

        $response->getBody()->write(json_encode($user->toArray()));

        // Benutzerdaten an die React-App zurückgeben
        return $response;
    }
}