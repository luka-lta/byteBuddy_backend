<?php

declare(strict_types=1);

namespace ByteBuddyApi\Service;

use ByteBuddyApi\Exception\ByteBuddyValidationException;

class ValidationService
{
    /**
     * @throws ByteBuddyValidationException
     */
    public function checkForRequiredBodyParams(array $requiredParams, ?array $actualParams): void
    {
        if (empty($requiredParams)) {
            throw new ByteBuddyValidationException('No required parameters provided', 400);
        }

        if (empty($actualParams)) {
            throw new ByteBuddyValidationException(
                'No parameters provided please use: [' . implode(', ', $requiredParams) . ']',
                400
            );
        }

        foreach ($requiredParams as $requiredParam) {
            if (!array_key_exists($requiredParam, $actualParams)) {
                throw new ByteBuddyValidationException("Missing required parameter: $requiredParam", 400);
            }
        }
    }
}
