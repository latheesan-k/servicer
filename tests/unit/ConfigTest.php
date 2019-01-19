<?php

namespace MVF\Servicer\Tests;

class ConfigTest extends \Codeception\Test\Unit
{
    public function testThatSkipReturnsFalse()
    {
        $config = $this->make(\MVF\Servicer\Config::class);
        self::assertEquals(false, $config->isCircuitBreakerClosed());
    }
}
