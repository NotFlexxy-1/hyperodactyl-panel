<?php

namespace Hyperodactyl\Events\Subuser;

use Hyperodactyl\Events\Event;
use Hyperodactyl\Models\Subuser;
use Illuminate\Queue\SerializesModels;

class Deleted extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Subuser $subuser)
    {
    }
}
