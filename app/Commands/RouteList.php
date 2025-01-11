<?php

declare(strict_types=1);

namespace App\Commands;

class RouteList extends SlimCommand
{
    protected static string $defaultName = 'route:list';

    protected function configure(): void
    {
        $this
            ->setAliases(['list'])
            ->setDescription('');

        parent::configure();
    }
}
