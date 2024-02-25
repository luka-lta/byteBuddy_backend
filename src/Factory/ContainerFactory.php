<?php
declare(strict_types=1);

namespace ByteBuddyApi\Factory;

use ByteBuddyApi\ApplicationConfig;
use DI\Container;
use DI\ContainerBuilder;

class ContainerFactory
{
    /**
     * @throws \Exception
     */
    public static function buildContainer(): Container
    {
        $container = new ContainerBuilder();
        $container->useAutowiring(true);
        $container->addDefinitions(new ApplicationConfig());
        return $container->build();
    }
}