<?php
/**
 * Created by PhpStorm.
 * User: drupsys
 * Date: 19/01/19
 * Time: 12:35.
 */

namespace MVF\Servicer\Queues\Tests;

class SqsQueueTest extends \Codeception\Test\Unit
{
    public function testThatSkipReturnsFalse()
    {
        $config = $this->make(\MVF\Servicer\Config::class);
        self::assertEquals(false, $config->skip());
    }
}
