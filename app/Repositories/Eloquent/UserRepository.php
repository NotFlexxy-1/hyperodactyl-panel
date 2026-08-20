<?php

namespace Hyperodactyl\Repositories\Eloquent;

use Hyperodactyl\Models\User;
use Hyperodactyl\Contracts\Repository\UserRepositoryInterface;

class UserRepository extends EloquentRepository implements UserRepositoryInterface
{
    /**
     * Return the model backing this repository.
     */
    public function model(): string
    {
        return User::class;
    }
}
