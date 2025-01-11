<?php

namespace App\Facades;

/**
 * @method static \Psr\Http\Message\ResponseInterface to(string $routeName, array $data = [], array $queryParams = [], int $status = 302)
 * @method static \Psr\Http\Message\ResponseInterface back()
 */
final class Redirect extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Supports\Redirect::class;
    }
}
