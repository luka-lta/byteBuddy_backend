<?php

use ByteBuddyApi\Factory\ContainerFactory;
use ByteBuddyApi\Middleware\PreflightMiddleware;
use ByteBuddyApi\Route\Routes;
use DI\Bridge\Slim\Bridge;

require __DIR__ . '/../vendor/autoload.php';

try {
    $container = ContainerFactory::buildContainer();
    $app = Bridge::create($container);

    if (getenv('APP_ENV') === 'development') {
        $app->addErrorMiddleware(true, true, true);
    }
    $app->addBodyParsingMiddleware();
    $app->add(new PreflightMiddleware());
    Routes::getRoutes($app);

    $app->run();
} catch (Exception $e) {
    echo $e->getMessage();
}
