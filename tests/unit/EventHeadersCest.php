<?php

use MVF\Servicer\Mocks\HeaderAMock;

class EventHeadersCest
{
    /**
     * @var array
     */
    private $headers;
    /**
     * @var HeaderAMock
     */
    private $mock;

    public function _before()
    {
        $this->mock = new HeaderAMock();
        $this->headers = ['event' => 'FROM_PAYLOAD', 'created_at' => 15];
    }

    public function eventNameShouldBeSetCorrectly(UnitTester $I)
    {
        $I->assertEquals('TEST_EVENT', $this->mock->getEvent());
    }

    public function eventVersionShouldBeSetCorrectly(UnitTester $I)
    {
        $I->assertEquals('1.0.0', $this->mock->getVersion());
    }

    public function createdAtShouldBeZeroByDefault(UnitTester $I)
    {
        $I->assertEquals(0, $this->mock->getCreatedAt());
    }

    public function shouldBeAbleToLoadEventNameFromPayload(UnitTester $I)
    {
        $this->mock->from($this->headers);
        $I->assertEquals('FROM_PAYLOAD', $this->mock->getEvent());
    }

    public function shouldBeAbleToLoadCreatedAtFromPayload(UnitTester $I)
    {
        $this->mock->from($this->headers);
        $I->assertEquals(15, $this->mock->getCreatedAt());
    }

    public function asd(UnitTester $I)
    {
        $I->assertArrayNotHasKey('created_at', $this->mock->toPayload());
    }
}
