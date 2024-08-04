<?php

declare(strict_types=1);

namespace ByteBuddyApi\V1\Service;

use ByteBuddyApi\V1\Exception\ByteBuddyTokenException;
use Exception;
use Firebase\JWT\JWT;

class JwtService
{
    public function generateNewToken(int $userId, string $userName): string
    {
        $payload = [
            'iss' => 'bytebuddy',
            'uid' => $userId,
            'name' => $userName,
            'iat' => time(),
            'exp' => time() + 3600
        ];
        $secretKey = getenv('JWT_SECRET');
        $algorithm = 'HS256';

        return JWT::encode($payload, $secretKey, $algorithm);
    }

    /**
     * @throws ByteBuddyTokenException
     */
    public function validateToken(string $token): bool
    {
        $secretKey = getenv('JWT_SECRET');
        $algorithm = 'HS256';

        try {
            $decoded = JWT::decode($token, $secretKey, [$algorithm]);

            if ($decoded->exp < time()) {
                throw new ByteBuddyTokenException('Token expired', 401);  // Specific exception
            }

            if ($decoded->iss !== 'bytebuddy') {
                throw new ByteBuddyTokenException('Invalid token issuer', 401); // Specific exception
            }

            if (!isset($decoded->uid) || !isset($decoded->name)) {
                throw new ByteBuddyTokenException('Required claims missing', 401); // Specific exception
            }

            if ($decoded->iat > time()) {
                throw new ByteBuddyTokenException('Invalid token issued at time', 401); // Specific exception
            }
        } catch (Exception) {
            throw new ByteBuddyTokenException('An error occurred on validate token', 500);
        }

        return true;
    }

    public function getUserDataFromToken(string $token): array
    {
        $secretKey = getenv('JWT_SECRET');
        $algorithm = 'HS256';

        $token = str_replace('Bearer ', '', $token);
        $decoded = JWT::decode($token, $secretKey, [$algorithm]);

        return [
            'uid' => $decoded->uid,
            'name' => $decoded->name,
        ];
    }

    /**
     * @throws ByteBuddyTokenException
     */
    public function validateAndDecodeToken(array|string $token): array
    {
        $this->validateToken($token);
        return $this->getUserDataFromToken($token);
    }
}
