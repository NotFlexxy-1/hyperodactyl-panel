<?php

namespace Hyperodactyl\Events\Auth;

use Hyperodactyl\Models\User;
use Hyperodactyl\Events\Event;

class ProvidedAuthenticationToken extends Event
{
    public function __construct(public User $user, public bool $recovery = false)
    {
    }
}
