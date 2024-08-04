<?php
declare(strict_types=1);

namespace ByteBuddyApi\V1\Action\Birthday;

use ByteBuddyApi\V1\Action\ByteBuddyAction;
use ByteBuddyApi\V1\Exception\ByteBuddyValidationException;
use ByteBuddyApi\V1\Service\Results\BirthdayService;
use ByteBuddyApi\V1\Service\ValidationService;
use ByteBuddyApi\V1\Value\Result;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BirthdayAction extends ByteBuddyAction
{
    public function __construct(
        private readonly BirthdayService   $birthdayService,
        private readonly ValidationService $validationService,
    ) {
    }

    public function handleGetBirthdaysFromGuildAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $this->validationService->checkForRequiredBodyParams(['guildId'], $request->getQueryParams());
            $result = $this->birthdayService->getBirthdays($request->getQueryParams()['guildId']);
        } catch (ByteBuddyValidationException $e) {
            $result = Result::from(false, $e->getMessage(), null, $e->getCode());
        }

        return $this->buildResponse($response, $result);
    }

    public function handleSetOrUpdateBirthdaysAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        try {
            $this->validationService->checkForRequiredBodyParams(['userId', 'birthdayDate'], $parsedBody);
            $result = $this->birthdayService->setOrUpdateBirthday(
                $request->getQueryParams()['guildId'],
                $parsedBody['userId'],
                $parsedBody['birthdayDate']
            );
        } catch (ByteBuddyValidationException $e) {
            $result = Result::from(false, $e->getMessage(), null, $e->getCode());
        }

        return $this->buildResponse($response, $result);
    }
}