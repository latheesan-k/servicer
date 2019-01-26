<?php

namespace MVF\Servicer\Tests;

class SettingsTest extends \Codeception\Test\Unit
{
    public function testThatSkipReturnsFalse()
    {
        $config = $this->make(\MVF\Servicer\Settings::class);
        self::assertEquals(false, $config->isCircuitBreakerClosed());
    }
}
