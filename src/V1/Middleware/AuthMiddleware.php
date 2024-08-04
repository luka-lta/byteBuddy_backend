<?php

declare(strict_types=1);

namespace ByteBuddyApi\V1\Middleware;

use ByteBuddyApi\V1\Exception\ByteBuddyException;
use ByteBuddyApi\V1\Service\JwtService;
use Firebase\JWT\ExpiredException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use Slim\Psr7\Stream;

class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly JwtService $jwtService
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = new Response();
        $status = 401;
        $authorization = $request->getHeader('Authorization');

        if (empty($authorization)) {
            $message = 'Authorization header is missing';
            return $this->unauthorizedResponse($response, $message, $status);
        }

        $token = str_replace('Bearer ', '', $authorization[0]);

        try {
            $decodedToken = $this->jwtService->validateAndDecodeToken($token);
            $request = $request->withAttribute('decodedToken', $decodedToken);

            return $handler->handle($request);
        } catch (ByteBuddyException $e) {
            $message = $e->getMessage();
            $status = $e->getCode();
        } catch (ExpiredException $e) {
            $message = 'Token is expired';
        }

        return $this->unauthorizedResponse($response, $message, $status);
    }

    private function unauthorizedResponse(ResponseInterface $response, string $message, int $status): ResponseInterface
    {
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
            ->withHeader('Access-Control-Max-Age', '86400')
            ->withBody(new Stream(fopen('php://temp', 'r+')));
    }
}
