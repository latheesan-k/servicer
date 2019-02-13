<?php
/**
 * Created by PhpStorm.
 * User: drupsys
 * Date: 26/01/19
 * Time: 21:52.
 */

namespace MVF\Servicer;

interface ConfigInterface
{
    public function getSettings(): SettingsInterface;

    public function getEvents(): Events;
}
