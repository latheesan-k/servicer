<?php

namespace MVF\Servicer\Tests;

class SettingsTest extends \Codeception\Test\Unit
{
    public function testThatSkipReturnsFalse()
    {
        $config = $this->makeEmpty(\MVF\Servicer\SettingsInterface::class);
        self::assertEquals(false, $config->isCircuitBreakerClosed());
    }
}
