<?php

namespace Hyperodactyl\Services\Users;

use Hyperodactyl\Models\User;
use Hyperodactyl\Models\Server;
use Hyperodactyl\Exceptions\DisplayException;

class UserDeletionService
{
    /**
     * Delete a user from the panel only if they have no servers attached to their account.
     *
     * @throws DisplayException
     */
    public function handle(int|User $user): ?bool
    {
        $user = $user instanceof User ? $user : User::query()->findOrFail($user);

        $servers = Server::query()->where('owner_id', $user->id)->count();
        if ($servers > 0) {
            throw new DisplayException(trans('admin/user.exceptions.user_has_servers'));
        }

        return $user->delete();
    }
}
