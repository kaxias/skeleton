<?php

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
