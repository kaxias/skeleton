<?php

declare(strict_types=1);

namespace App\Commands;

use Symfony\Component\Console\Command\Command;

abstract class SlimCommand extends Command
{
    public function __construct(string|null $name = null)
    {
        parent::__construct($name);
    }
}
