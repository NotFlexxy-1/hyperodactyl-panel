<?php

namespace Hyperodactyl\Repositories\Eloquent;

use Hyperodactyl\Models\RecoveryToken;

class RecoveryTokenRepository extends EloquentRepository
{
    public function model(): string
    {
        return RecoveryToken::class;
    }
}
