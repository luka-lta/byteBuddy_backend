<?php

declare(strict_types=1);

namespace ByteBuddyApi\Service\Results;

use ByteBuddyApi\Value\Result;

class HealthService
{
    public function checkApiHealth(): Result
    {
        return Result::from(true, 'API is healthy', null, 200);
    }
}
