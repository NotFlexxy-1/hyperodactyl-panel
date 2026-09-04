<?php

namespace Hyperodactyl\Listeners;

use Hyperodactyl\Facades\Activity;
use Illuminate\Contracts\Events\Dispatcher;
use Hyperodactyl\Events\Auth\ProvidedAuthenticationToken;
use Hyperodactyl\Extensions\Illuminate\Events\Contracts\SubscribesToEvents;

class TwoFactorListener implements SubscribesToEvents
{
    public function __invoke(ProvidedAuthenticationToken $event): void
    {
        Activity::event($event->recovery ? 'auth:recovery-token' : 'auth:token')
            ->withRequestMetadata()
            ->subject($event->user)
            ->log();
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(ProvidedAuthenticationToken::class, self::class);
    }
}
