<?php

declare(strict_types=1);

namespace ByteBuddyApi\V1\Factory;

use ByteBuddyApi\ApplicationConfig;
use DI\Container;
use DI\ContainerBuilder;
use Exception;

class ContainerFactory
{
    /**
     * @throws Exception
     */
    public static function buildContainer(): Container
    {
        $container = new ContainerBuilder();
        $container->useAutowiring(true);
        $container->addDefinitions(new ApplicationConfig());
        return $container->build();
    }
}
