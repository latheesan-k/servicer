<?php

use MVF\Servicer\Mocks\EventAMock;
use MVF\Servicer\Mocks\EventBMock;

class EventPayloadCest
{
    /**
     * @var EventAMock
     */
    private $mock;

    public function _before()
    {
        $this->mock = new EventAMock();
        $this->mock->firstName = 'Bob';
    }

    public function shouldConvertPrivateAttributesToSnakeCase(UnitTester $I)
    {
        $I->assertArrayHasKey('first_name', $this->mock->toPayload());
    }

    public function transformedKeyShouldHaveCorrectValue(UnitTester $I)
    {
        $I->assertEquals('Bob', $this->mock->toPayload()['first_name']);
    }

    public function throwExceptionIfInvalidObjectAttributeIsProvided(UnitTester $I)
    {
        $I->expectExceptionMessage('Invalid object attribute found', function () use ($I) {
            $this->mock->invalidObject = (object)[];
            $I->assertFalse(is_array($this->mock->toPayload()));
        });
    }

    public function createdAtShouldBeZeroByDefault(UnitTester $I)
    {
        $this->mock->address = new EventBMock();
        $I->assertArrayHasKey('address', $this->mock->toPayload());
    }

    public function shouldTransformPrivateAttributesAsWell(UnitTester $I)
    {
        $I->assertArrayHasKey('private_attribute', $this->mock->toPayload());
    }
}
