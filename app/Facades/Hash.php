<?php

namespace App\Facades;

/**
 * @method static string md2(string $string, bool $binary = false)
 * @method static string md4(string $string, bool $binary = false)
 * @method static string md5(string $string, bool $binary = false)
 * @method static string sha1(string $string, bool $binary = false)
 * @method static string sha256(string $string, bool $binary = false)
 * @method static string sha384(string $string, bool $binary = false)
 * @method static string sha512(string $string, bool $binary = false)
 * @method static string adler32(string $string, bool $binary = false)
 * @method static string crc32(string $string, bool $binary = false)
 * @method static string crc32b(string $string, bool $binary = false)
 */
final class Hash extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Supports\Hash::class;
    }
}
