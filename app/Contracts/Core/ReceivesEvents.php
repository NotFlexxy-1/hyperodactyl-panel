<?php

namespace Hyperodactyl\Contracts\Core;

use Hyperodactyl\Events\Event;

interface ReceivesEvents
{
    /**
     * Handles receiving an event from the application.
     */
    public function handle(Event $notification): void;
}
