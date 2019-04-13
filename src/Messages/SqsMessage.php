<?php

namespace MVF\Servicer\Messages;

use MVF\Servicer\EventPayload;

class SqsMessage implements EventMessageInterface
{
    use EventPayload;

    private $url;
    private $delaySeconds;

    /**
     * SqsMessage constructor.
     *
     * @param string $url          Of the SQS queue
     * @param int    $delaySeconds The amount of time the message should be delayed
     */
    public function __construct(string $url, int $delaySeconds = 0)
    {
        $this->url = $url;
        $this->delaySeconds = $delaySeconds;
    }

    /**
     * Returns the type of the provider.
     *
     * @return string
     */
    public function getProvider(): string
    {
        return 'SQS';
    }

    /**
     * Get the SQS queue url.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Get the amount of time the message will be delayed.
     *
     * @return int
     */
    public function getDelaySeconds(): int
    {
        return $this->delaySeconds;
    }
}
