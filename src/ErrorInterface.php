<?php
/**
 * Created by PhpStorm.
 * User: drupsys
 * Date: 24/12/18
 * Time: 12:24.
 */

namespace MVF\Servicer;

interface ErrorInterface
{
    public function handleException(\Exception $exception): void;
}
