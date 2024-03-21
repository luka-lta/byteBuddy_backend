<?php
declare(strict_types=1);

namespace ByteBuddyApi\Service;

use ByteBuddyApi\Value\ResultObject;

class HealthService
{
    public function checkApiHealth(): ResultObject
    {
        return ResultObject::from(true, 'API is healthy', null, 200);
    }
}