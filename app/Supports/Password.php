<?php

namespace App\Supports;

final class Password
{
    protected string $algo = PASSWORD_BCRYPT;

    protected array $options = [
        'cost' => 8
    ];

    public function hash(string $password): string
    {
        return password_hash($password, $this->algo, $this->options);
    }

    public function verify(string $password, string|null $hash): bool
    {
        if (is_null($hash) || strlen($hash) === 0) {
            return false;
        }

        return password_verify($password, $hash);
    }

    public function info($hashedValue): array
    {
        return password_get_info($hashedValue);
    }
}
