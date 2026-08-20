<?php

namespace Hyperodactyl\Events\Server;

use Hyperodactyl\Events\Event;
use Hyperodactyl\Models\Server;
use Illuminate\Queue\SerializesModels;

class Saved extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Server $server)
    {
    }
}
