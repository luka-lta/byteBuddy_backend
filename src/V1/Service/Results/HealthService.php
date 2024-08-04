<?php

declare(strict_types=1);

namespace ByteBuddyApi\V1\Service\Results;

use ByteBuddyApi\V1\Value\Result;

class HealthService
{
    public function checkApiHealth(): Result
    {
        return Result::from(true, 'API is healthy', null, 200);
    }
}
