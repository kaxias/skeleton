<?php

use App\ContainerFactory;
use Slim\Builder\AppBuilder;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

new Dotenv()->loadEnv(dirname(__DIR__) . '/storage/.env');

$builder = new AppBuilder();

$builder->setContainerFactory(new ContainerFactory());

return $builder->build()->getContainer();
