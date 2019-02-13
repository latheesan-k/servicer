<?php

include __DIR__.'/vendor/autoload.php';

use MVF\Servicer\Commands\ExecCommand;
use Symfony\Component\Console\Application;



$app = new Application();

$daemon = new ExecCommand();
$app->addCommands([$daemon]);
$app->run();
