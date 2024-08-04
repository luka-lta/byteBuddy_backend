<?php

declare(strict_types=1);

namespace ByteBuddyApi\V2\Action;

use ByteBuddyApi\V1\Exception\ByteBuddyException;
use ByteBuddyApi\V2\Value\ErrorResult;
use ByteBuddyApi\V2\Value\GeneralResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

abstract class ByteBuddyAction
{
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        try {
            $response = $this->execute($request, $response);
        } catch (ByteBuddyException $exception) {
            $result = GeneralResult::from(
                ErrorResult::from($exception),
                $exception->getCode()
            );

            return $result->getResponse($response);
        } catch (Throwable $exception) {
            $result = GeneralResult::from(
                ErrorResult::from($exception),
                500
            );

            return $result->getResponse($response);
        }

        return $response;
    }

    abstract protected function execute(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface;
}
