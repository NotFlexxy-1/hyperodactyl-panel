<?php

namespace Hyperodactyl\Events\User;

use Hyperodactyl\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

final class PasswordChanged
{
    use Dispatchable;

    public function __construct(public readonly User $user)
    {
    }
}
