<?php

namespace MVF\Servicer;

interface ConfigInterface
{
    /**
     * Gets the settings class.
     *
     * @return SettingsInterface
     */
    public function getSettings(): SettingsInterface;

    /**
     * Gets the events class.
     *
     * @return Events
     */
    public function getEvents(): Events;
}
