<?php

use App\Facades\Facade;
use Mrcl\SlimRoutes\SlimRoutes;
use Slim\App;
use Slim\Middleware;

(require dirname(__DIR__) . '/bootstrap/container.php')->call(function (
    App        $app,
    SlimRoutes $slimRoutes,
) {
    Facade::setContainer($app->getContainer());

    $app->add(Middleware\RoutingMiddleware::class);
    $app->add(Middleware\BodyParsingMiddleware::class);
    $app->add(Middleware\ExceptionHandlingMiddleware::class);
    $app->add(Middleware\ExceptionLoggingMiddleware::class);
    $app->add(Middleware\EndpointMiddleware::class);

    $slimRoutes->registerRoutes();
    $slimRoutes->setFileNamePattern('.+Controller', 'php|PHP');

    return $app;
})->run();
