<?php

namespace App\Supports;

final class Hash
{
    public function md2(string $string, bool $binary = false): string
    {
        return hash('md2', $string, $binary);
    }

    public function md4(string $string, bool $binary = false): string
    {
        return hash('md4', $string, $binary);
    }

    public function md5(string $string, bool $binary = false): string
    {
        return hash('md5', $string, $binary);
    }

    public function sha1(string $string, bool $binary = false): string
    {
        return hash('sha1', $string, $binary);
    }

    public function sha256(string $string, bool $binary = false): string
    {
        return hash('sha256', $string, $binary);
    }

    public function sha384(string $string, bool $binary = false): string
    {
        return hash('sha384', $string, $binary);
    }

    public function sha512(string $string, bool $binary = false): string
    {
        return hash('sha512', $string, $binary);
    }

    public function adler32(string $string, bool $binary = false): string
    {
        return hash('adler32', $string, $binary);
    }

    public function crc32(string $string, bool $binary = false): string
    {
        return hash('crc32', $string, $binary);
    }

    public function crc32b(string $string, bool $binary = false): string
    {
        return hash('crc32b', $string, $binary);
    }
}
