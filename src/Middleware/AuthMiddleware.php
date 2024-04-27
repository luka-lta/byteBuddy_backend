<?php

declare(strict_types=1);

namespace ByteBuddyApi\Middleware;

use ByteBuddyApi\Exception\ByteBuddyException;
use ByteBuddyApi\Service\JwtService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly JwtService $jwtService
    )
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = new Response();
        $message = '';
        $status = 401;
        $authorization = $request->getHeader('Authorization');
        if (empty($authorization)) {
            $message = 'Authorization header is missing';
        }

        $token = str_replace('Bearer ', '', $authorization[0]);
        try {
            $validate = $this->jwtService->validateToken($token);
            if ($validate) {
                return $handler->handle($request);
            }
        } catch (ByteBuddyException $e) {
            $message = $e->getMessage();
            $status = $e->getCode();
        }
        $resultMessage = [
            'success' => false,
            'message' => $message,
            'statusCode' => $status,
        ];
        $jsonMessage = json_encode($resultMessage);
        $response->getBody()->write($jsonMessage);
        return $response
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', '*')
            ->withHeader('Access-Control-Max-Age', '86400');
    }
}
