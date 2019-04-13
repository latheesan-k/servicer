<?php

use MVF\Servicer\Messages\SnsMessage;

class SnsMessageCest
{
    /**
     * @var SnsMessage
     */
    private $message;

    public function _before()
    {
        $this->message = new SnsMessage('test_arn');
    }

    public function getProviderShouldReturnSNS(UnitTester $I)
    {
        $I->assertEquals('SNS', $this->message->getProvider());
    }

    public function getArnShouldReturnTheProvidedArn(UnitTester $I)
    {
        $I->assertEquals('test_arn', $this->message->getArn());
    }
}
