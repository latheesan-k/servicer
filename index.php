<?php

include __DIR__.'/vendor/autoload.php';

use MVF\Servicer\Commands\DaemonCommand;
use Symfony\Component\Console\Application;

$daemon = new DaemonCommand();
$app = new Application();
$app->addCommands([$daemon]);
$app->run();
