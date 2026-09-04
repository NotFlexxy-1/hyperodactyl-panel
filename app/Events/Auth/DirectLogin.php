<?php

namespace Hyperodactyl\Events\Auth;

use Hyperodactyl\Models\User;
use Hyperodactyl\Events\Event;

class DirectLogin extends Event
{
    public function __construct(public User $user, public bool $remember)
    {
    }
}
