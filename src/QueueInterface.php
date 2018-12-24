<?php
/**
 * Created by PhpStorm.
 * User: drupsys
 * Date: 24/12/18
 * Time: 00:47
 */

namespace MVF\Servicer;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface QueueInterface
{
    function listen(ActionsInterface $actions, InputInterface $input, OutputInterface $output): void;
}