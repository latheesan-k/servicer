<?php

namespace MVF\Servicer;

trait EventHeaders
{
    private $event;
    private $version;
    private $createdAt = 0;

    /**
     * Get the name of the event.
     *
     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * Get the version of the event.
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Get the unix timestamp this event was created at.
     *
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    /**
     * Initialises header attributes.
     *
     * @param string $event   Event name
     * @param string $version Event version
     */
    private function initialise(string $event, string $version)
    {
        $this->event = $event;
        $this->version = $version;
    }

    /**
     * Sets header attributes using provided array.
     *
     * @param array $headers Array with expected header attributes
     */
    private function loadRemaining(array $headers)
    {
        $this->event = $headers['event'];
        $this->createdAt = $headers['created_at'];
    }
}
