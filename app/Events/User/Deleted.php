<?php

namespace Hyperodactyl\Events\User;

use Hyperodactyl\Models\User;
use Hyperodactyl\Events\Event;
use Illuminate\Queue\SerializesModels;

class Deleted extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public User $user)
    {
    }
}
