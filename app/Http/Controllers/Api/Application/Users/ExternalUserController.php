<?php

namespace Hyperodactyl\Http\Controllers\Api\Application\Users;

use Hyperodactyl\Models\User;
use Hyperodactyl\Transformers\Api\Application\UserTransformer;
use Hyperodactyl\Http\Controllers\Api\Application\ApplicationApiController;
use Hyperodactyl\Http\Requests\Api\Application\Users\GetExternalUserRequest;

class ExternalUserController extends ApplicationApiController
{
    /**
     * Retrieve a specific user from the database using their external ID.
     */
    public function index(GetExternalUserRequest $request, string $external_id): array
    {
        $user = User::query()->where('external_id', $external_id)->firstOrFail();

        return $this->fractal->item($user)
            ->transformWith($this->getTransformer(UserTransformer::class))
            ->toArray();
    }
}
