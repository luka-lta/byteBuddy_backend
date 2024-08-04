<?php
declare(strict_types=1);

namespace ByteBuddyApi\V1\Action\Health;

use ByteBuddyApi\V1\Action\ByteBuddyAction;
use ByteBuddyApi\V1\Service\Results\HealthService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HealthAction extends ByteBuddyAction
{
    public function __construct(
        private readonly HealthService $healthService
    )
    {
    }

    public function handleHealthAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $result = $this->healthService->checkApiHealth();
        return $this->buildResponse($response, $result);
    }
}