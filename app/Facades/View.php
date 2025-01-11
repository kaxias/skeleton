<?php

namespace App\Facades;

/**
 * @method static \Psr\Http\Message\ResponseInterface twig(string $template, array $data = [])
 * @method static \Psr\Http\Message\ResponseInterface json(array|object $data, array $context = [], int $status = 200, string $format = 'json')
 */
final class View extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Supports\View::class;
    }
}
