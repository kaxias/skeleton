<?php

declare(strict_types=1);

namespace App\Supports\Twig;

use DI\Attribute\Inject;
use DI\Container;
use Twig\RuntimeLoader\RuntimeLoaderInterface;

class TwigRuntimeLoader implements RuntimeLoaderInterface
{
    #[Inject]
    protected Container $container;

    public function load(string $class): mixed
    {
        return $this->container->has($class) ? $this->container->get($class) : null;
    }
}
