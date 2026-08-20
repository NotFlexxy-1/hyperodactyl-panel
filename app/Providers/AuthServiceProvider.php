<?php

namespace Hyperodactyl\Providers;

use Laravel\Sanctum\Sanctum;
use Hyperodactyl\Models\ApiKey;
use Hyperodactyl\Models\Server;
use Hyperodactyl\Models\LxcContainer;
use Hyperodactyl\Policies\LxcContainerPolicy;
use Hyperodactyl\Policies\ServerPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     */
    protected $policies = [
        Server::class => ServerPolicy::class,
        LxcContainer::class => LxcContainerPolicy::class,
    ];

    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(ApiKey::class);
    }
}
