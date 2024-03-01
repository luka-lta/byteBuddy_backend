<?php
declare(strict_types=1);

namespace ByteBuddyApi\Action\Birthday;

use ByteBuddyApi\Action\ByteBuddyAction;
use ByteBuddyApi\Service\BirthdayService;
use ByteBuddyApi\Value\ResultObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BirthdayAction extends ByteBuddyAction
{
    public function __construct(
        private readonly BirthdayService $birthdayService,
    )
    {
    }

    public function handleGetBirthdaysFromGuildAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $guildId = $request->getQueryParams()['guildId'] ?? null;

        if (!$guildId) {
            $result = ResultObject::from(false, 'Guild ID is required', null, 400);
            return $this->buildResponse($response, $result);
        }

        $result = $this->birthdayService->getBirthdays($guildId);
        return $this->buildResponse($response, $result);
    }

    public function handleSetOrUpdateBirthdaysAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();
        $guildId = $request->getQueryParams()['guildId'] ?? null;
        $birthdayString = $parsedBody['birthdayDate'] ?? null;
        $userId = $parsedBody['userId'] ?? null;

        if (!$guildId) {
            $result = ResultObject::from(false, 'Guild ID is required', null, 400);
            return $this->buildResponse($response, $result);
        }

        if (!$userId) {
            $result = ResultObject::from(false, 'User ID is required', null, 400);
            return $this->buildResponse($response, $result);
        }

        if (!$birthdayString) {
            $result = ResultObject::from(false, 'Birthday date is required', null, 400);
            return $this->buildResponse($response, $result);
        }

        $result = $this->birthdayService->setOrUpdateBirthday($guildId, $parsedBody['userId'], $parsedBody['birthdayDate']);
        return $this->buildResponse($response, $result);
    }
}