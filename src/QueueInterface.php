<?php
/**
 * Created by PhpStorm.
 * User: drupsys
 * Date: 24/12/18
 * Time: 00:47
 */

namespace MVF\Servicer;

interface QueueInterface
{
    function listen(ActionsInterface $actions, ErrorInterface $error): void;
}