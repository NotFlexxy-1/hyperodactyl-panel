<?php

namespace Hyperodactyl\Providers;

use Hyperodactyl\Models\User;
use Hyperodactyl\Models\Server;
use Hyperodactyl\Models\Subuser;
use Hyperodactyl\Models\EggVariable;
use Hyperodactyl\Observers\UserObserver;
use Hyperodactyl\Observers\ServerObserver;
use Hyperodactyl\Observers\SubuserObserver;
use Hyperodactyl\Listeners\TwoFactorListener;
use Hyperodactyl\Listeners\RevocationListener;
use Hyperodactyl\Observers\EggVariableObserver;
use Hyperodactyl\Listeners\AuthenticationListener;
use Hyperodactyl\Events\Server\Installed as ServerInstalledEvent;
use Hyperodactyl\Notifications\ServerInstalled as ServerInstalledNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     */
    protected $listen = [
        ServerInstalledEvent::class => [ServerInstalledNotification::class],
    ];

    protected $subscribe = [
        AuthenticationListener::class,
        RevocationListener::class,
        TwoFactorListener::class,
    ];

    protected static $shouldDiscoverEvents = false;

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();

        User::observe(UserObserver::class);
        Server::observe(ServerObserver::class);
        Subuser::observe(SubuserObserver::class);
        EggVariable::observe(EggVariableObserver::class);
    }
}
