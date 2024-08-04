<?php

declare(strict_types=1);

namespace ByteBuddyApi\V2\Slim;

use Monolog\Logger;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Throwable;

class ErrorHandler
{
    public function __construct(
        private readonly Logger $logger,
    ) {
    }

    public function handleError(
        Throwable              $exception,
        ResponseInterface      $response,
        ServerRequestInterface $request,
        bool                   $displayErrorDetails,
    ): ResponseInterface {
        $this->logError($exception, $request);
        $statusCode = 500;
        $errorMessage = 'Internal server error';
        switch ($exception::class) {
            case HttpMethodNotAllowedException::class:
                $statusCode = 405;
                $errorMessage = 'Method not allowed';
                break;
            case HttpNotFoundException::class:
                $statusCode = 404;
                $errorMessage = 'Not found';
                break;
        }

        $payload = [
            'status' => 'error',
            'error' => 'App env is neither production nor development, no errors logged',
        ];

        if (getenv('APP_ENV') === 'development') {
            $payload = [
                'status' => $statusCode,
                'class' => $exception::class,
                'error' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ];
        }

        if (getenv('APP_ENV') === 'production') {
            $payload = [
                'status' => $statusCode,
            ];
            if ($displayErrorDetails) {
                $payload['error'] = $errorMessage;
            }
        }

        try {
            $jsonPayload = json_encode($payload);
            $response->getBody()->write($jsonPayload);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage(), ['error' => $e]);
        }

        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    }

    public function logError(Throwable $exception, RequestInterface $request): void
    {
        $uri = $request->getUri();

        $context = [
            'error' => $exception,
            'method' => $request->getMethod(),
            'uri' => $uri->getPath(),
            'query' => $uri->getQuery(),
        ];

        switch ($exception::class) {
            case HttpMethodNotAllowedException::class:
                $this->logger->warning('Api called with forbidden method', $context);
                break;
            case HttpNotFoundException::class:
                $this->logger->warning('Api called with invalid path', $context);
                break;
            default:
                $this->logger->error($exception->getMessage(), $context);
        }
    }
}
