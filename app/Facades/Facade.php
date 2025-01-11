<?php

declare(strict_types=1);

namespace App\Facades;

use Psr\Container\ContainerInterface;

abstract class Facade
{
    private static array $resolvedInstances;
    private static ContainerInterface $container;

    public static function setContainer(ContainerInterface $container): void
    {
        static::$container = $container;
    }

    public static function __callStatic(string $method, array $args): mixed
    {
        $resolvedInstance = static::resolveInstance();

        if (!method_exists($resolvedInstance, $method)) {
            throw new \BadMethodCallException(
                sprintf('Method "%s" does not exist on resolved instance.', $method)
            );
        }

        return $resolvedInstance->{$method}(...$args);
    }

    private static function resolveInstance(): mixed
    {
        $accessor = static::getFacadeAccessor();

        if (!$accessor) {
            throw new \RuntimeException('Facade accessor is not defined.');
        }

        return static::getCachedInstance($accessor);
    }

    private static function getCachedInstance(string $accessor): mixed
    {
        if (!isset(static::$resolvedInstances[$accessor])) {
            if (!static::$container || !static::$container->has($accessor)) {
                throw new \RuntimeException("No entry found for {$accessor} in the container.");
            }

            static::$resolvedInstances[$accessor] = static::$container->get($accessor);
        }

        return static::$resolvedInstances[$accessor];
    }

    protected static function getFacadeAccessor(): string
    {
        throw new \RuntimeException('Subclasses must override getFacadeAccessor.');
    }
}
