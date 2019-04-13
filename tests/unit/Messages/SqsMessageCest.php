<?php

use MVF\Servicer\Messages\SqsMessage;

class SqsMessageCest
{
    /**
     * @var SqsMessage
     */
    private $message;

    public function _before()
    {
        $this->message = new SqsMessage('test_url');
    }

    public function getProviderShouldReturnSQS(UnitTester $I)
    {
        $I->assertEquals('SQS', $this->message->getProvider());
    }

    public function getUrlShouldReturnTheProvidedUrl(UnitTester $I)
    {
        $I->assertEquals('test_url', $this->message->getUrl());
    }

    public function getDelaySecondsShouldReturnTheZeroByDefault(UnitTester $I)
    {
        $I->assertEquals(0, $this->message->getDelaySeconds());
    }

    public function getDelaySecondsShouldReturnProvidedValueIfItWasSet(UnitTester $I)
    {
        $this->message = new SqsMessage('test_url', 15);
        $I->assertEquals(15, $this->message->getDelaySeconds());
    }
}
