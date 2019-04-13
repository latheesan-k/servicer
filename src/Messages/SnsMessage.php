<?php

namespace MVF\Servicer\Messages;

use MVF\Servicer\EventPayload;

class SnsMessage implements EventMessageInterface
{
    use EventPayload;

    private $arn;

    /**
     * SnsMessage constructor.
     *
     * @param string $arn Of the SNS topic
     */
    public function __construct(string $arn)
    {
        $this->arn = $arn;
    }

    /**
     * Returns the type of the provider.
     *
     * @return string
     */
    public function getProvider(): string
    {
        return 'SNS';
    }

    /**
     * Returns the arn of the SNS topic.
     *
     * @return string
     */
    public function getArn(): string
    {
        return $this->arn;
    }
}
