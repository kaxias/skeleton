<?php

namespace App\Facades;

/**
 * @method static string hash(string $password)
 * @method static bool verify(string $password, string|null $hash)
 */
final class Password extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Supports\Password::class;
    }
}
