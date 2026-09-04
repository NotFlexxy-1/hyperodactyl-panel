<?php

namespace Hyperodactyl\Policies;

use Hyperodactyl\Models\User;
use Hyperodactyl\Models\LxcContainer;

class LxcContainerPolicy
{
    public function view(User $user, LxcContainer $container): bool
    {
        return $user->root_admin || $user->id === $container->owner_id;
    }

    public function update(User $user, LxcContainer $container): bool
    {
        return $user->root_admin || $user->id === $container->owner_id;
    }

    public function delete(User $user, LxcContainer $container): bool
    {
        return $user->root_admin || $user->id === $container->owner_id;
    }
}
