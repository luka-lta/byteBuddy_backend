<?php

use ByteBuddyApi\V1\Factory\ContainerFactory;
use ByteBuddyApi\V2\Slim\SlimAppFactory;

require __DIR__ . '/../vendor/autoload.php';

try {
    $container = ContainerFactory::buildContainer();
    $app = SlimAppFactory::create($container);
    $app->run();
} catch (Exception $e) {
    echo $e->getMessage();
}
