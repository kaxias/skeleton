<?php

//use \App\Commands as SlimCommand;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Tools\Console\Command;

return static fn(DependencyFactory $dependencyFactory): array => [
    new Command\CurrentCommand($dependencyFactory),
    new Command\DumpSchemaCommand($dependencyFactory),
    new Command\ExecuteCommand($dependencyFactory),
    new Command\GenerateCommand($dependencyFactory),
    new Command\LatestCommand($dependencyFactory),
    new Command\MigrateCommand($dependencyFactory),
    new Command\RollupCommand($dependencyFactory),
    new Command\StatusCommand($dependencyFactory),
    new Command\VersionCommand($dependencyFactory),
    new Command\UpToDateCommand($dependencyFactory),
    new Command\SyncMetadataCommand($dependencyFactory),
    new Command\ListCommand($dependencyFactory),
    new Command\DiffCommand($dependencyFactory),

    // Slim Commands
//    new SlimCommand\RouteList()
];
